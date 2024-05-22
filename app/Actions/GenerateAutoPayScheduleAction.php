<?php

namespace App\Actions;

use App\Models\AuditSubMdaSchedule;
use App\Models\Domain;
use App\Models\FidelityLoanDeduction;
use App\Models\MicroFinanceBank;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GenerateAutoPayScheduleAction
{
    protected $year;

    protected $month;

    protected $domain;

    protected $payment;

    protected $narration;

    protected $reference;

    protected $reference_id = 1;

    protected $pay_comm_i;

    protected $pay_comm_ii;

    protected $fidelityLoan;

    protected $pay_comm_i_amount;

    protected $pay_comm_ii_amount;

    protected $pay_comm_i_charge;

    protected $pay_comm_ii_charge;

    protected const INTERSWITCH_CHARGE = 16.13;

    protected AuditSubMdaSchedule $sub_mda;

    public function execute(Domain $domain, AuditSubMdaSchedule $sub_mda)
    {
        $this->domain = $domain;

        $this->sub_mda = $sub_mda;

        $this->initializePayComms();

        DB::transaction(function () {
            $this->generateAutoPaySchedule();
        });

        $this->sub_mda->autopayGenerated();
    }

    private function generateAutoPaySchedule()
    {
        $schedules = $this->sub_mda->auditPaySchedules;

        $schedule = $schedules->first();

        $this->year = $schedule->month->year;
        $this->month = $schedule->month->monthName;
        $this->payment = Str::upper($schedule->auditPayrollCategory()->payment_type_id);

        [$commercial_schedules, $microfinance_schedules] = $schedules->partition(fn (
            $schedule
        ) => $schedule->bankable_type == 'commercial');

        /**
         * Commercial Bank Users
         * ___________________________________________________
         */
        $commercial_users = $commercial_schedules->count();

        $this->pay_comm_i_amount = $this->pay_comm_i_charge * $commercial_users;
        $this->pay_comm_ii_amount = $this->pay_comm_ii_charge * $commercial_users;

        foreach ($commercial_schedules as $schedule) {
            $amount = $schedule->net_pay - $this->pay_comm_i_charge - $this->pay_comm_ii_charge;

            if ($schedule->loan->count() > 0) {
                $loans = $schedule->loan->where('status', 'A');

                foreach ($loans as $loan) {
                    if ($loan->isNotPaid()) {
                        $loan_amount = $loan->collection_amount;

                        $amountCheck = $amount - $loan_amount - self::INTERSWITCH_CHARGE;

                        if($amountCheck > 0) {
                            $amount = $amountCheck;
                            $this->pay_comm_ii_amount += self::INTERSWITCH_CHARGE;

                            $loan->deductions()->create([
                                'amount' => $loan_amount,
                                'loan_account' => $loan->account_number,
                                'audit_sub_mda_schedule_id' => $this->sub_mda->id,
                            ]);
                        }
                    }

                    if ($loan->isPaid()) {
                        $loan->markAsPaid();
                    }
                }
            }

            $this->reference = $this->getReferenceFor($schedule->id);

            if (! $this->narration) {
                $this->narration = $this->createNarration($schedule->auditSubMdaSchedule->sub_mda_name);
            }

            $attributes = [
                'payment_reference' => $this->reference,
                'beneficiary_code'  => $schedule->account_number,
                'beneficiary_name'  => $schedule->beneficiary_name,
                'account_number'    => $schedule->account_number,
                'account_type'      => 10,
                'cbn_code'          => $schedule->bankable->code,
                'is_cash_card'      => '0',
                'narration'         => $this->narration,
                'amount'            => $amount,
                'email'             => ' ',
                'currency'          => 'NGN',
            ];

            $autopay_schedule = $this->sub_mda->autopaySchedules()->create($attributes);
            $schedule->autopay_schedule_id = $autopay_schedule->id;
            $schedule->save();
        }

        $ignore = MicroFinanceBank::where('name', '=', 'CASH PAYMENT')->first();

        /**
         * Microfinance Bank Users
         * ___________________________________________________
         */
        foreach ($microfinance_schedules as $schedule) {
            if ($schedule->bankable_id == $ignore->id) {
                continue;
            }

            $amount = $schedule->net_pay - $this->pay_comm_i_charge - $this->pay_comm_ii_charge - self::INTERSWITCH_CHARGE;

            $this->reference = $this->getReferenceFor($schedule->id);

            if (! $this->narration) {
                $this->narration = $this->createNarration($schedule->auditSubMdaSchedule->sub_mda_name);
            }

            $attributes = [
                'micro_finance_bank_id' => $schedule->bankable_id,
                'payment_reference'     => $this->reference,
                'beneficiary_code'      => $schedule->account_number,
                'beneficiary_name'      => $schedule->beneficiary_name,
                'account_number'        => $schedule->account_number,
                'account_type'          => 10,
                'cbn_code'              => $schedule->bankable->bankCode(),
                'is_cash_card'          => '0',
                'narration'             => $this->narration,
                'amount'                => $amount,
                'email'                 => ' ',
                'currency'              => 'NGN',
            ];

            $autopay_schedule = $this->sub_mda->microfinanceSchedules()->create($attributes);
            $schedule->autopay_schedule_id = $autopay_schedule->id;
            $schedule->save();
        }

        $mfbs = $microfinance_schedules->groupBy('bankable_id');

        foreach ($mfbs as $mfb) {
            $schedule = $mfb->first();

            if ($schedule->bankable_id == $ignore->id) {
                continue;
            }

            $mfb_users = $mfb->count();
            $sum_net_pay = $mfb->sum('net_pay');

            $paycomm_i = $this->pay_comm_i_charge * $mfb_users;
            $paycomm_ii = ($this->pay_comm_ii_charge + self::INTERSWITCH_CHARGE) * $mfb_users
                - self::INTERSWITCH_CHARGE;

            $amount = $sum_net_pay - $paycomm_i - $paycomm_ii;

            $bank = $schedule->bankable;

            if (! $this->narration) {
                $this->narration = $this->createNarration($schedule->auditSubMdaSchedule->sub_mda_name);
            }

            $attributes = [
                'payment_reference' => $this->getReferenceFor($schedule->id),
                'beneficiary_code'  => $bank->account_number,
                'beneficiary_name'  => $bank->name,
                'account_number'    => $bank->account_number,
                'account_type'      => 10,
                'cbn_code'          => $bank->bankCode(),
                'is_cash_card'      => '0',
                'narration'         => $this->narration,
                'amount'            => $amount,
                'email'             => ' ',
                'currency'          => 'NGN',
            ];

            $this->pay_comm_i_amount = $this->pay_comm_i_amount + $paycomm_i;
            $this->pay_comm_ii_amount = $this->pay_comm_ii_amount + $paycomm_ii;

            $this->sub_mda->autopaySchedules()->create($attributes);
        }

        if ($this->narration !== null) {
            /**
             * Fidelity Loan
             * ___________________________________________________
             */
            if ($this->sub_mda->fidelityDeductions->count() > 0) {
                $fidelityLoanAmount = $this->sub_mda->fidelityLoanAmount() + self::INTERSWITCH_CHARGE;
                $this->pay_comm_ii_amount -= self::INTERSWITCH_CHARGE;

                $fidelityLoan = [
                    'payment_reference' => $this->getReferenceFor($this->reference_id),
                    'beneficiary_code'  => $this->fidelityLoan->account_number,
                    'beneficiary_name'  => $this->fidelityLoan->code,
                    'account_number'    => $this->fidelityLoan->account_number,
                    'account_type'      => 10,
                    'cbn_code'          => $this->fidelityLoan->bankable->bankCode(),
                    'is_cash_card'      => '0',
                    'narration'         => substr($this->narration, 0, 16),
                    'amount'            => $fidelityLoanAmount,
                    'email'             => ' ',
                    'currency'          => 'NGN',
                ];

                $this->sub_mda->autopaySchedules()->create($fidelityLoan);

                $fidelitySchedule = $this->sub_mda->fidelitySchedules()->create($fidelityLoan);

                $this->sub_mda->fidelityDeductions()->update([
                    'fidelity_loan_schedule_id' => $fidelitySchedule->id
                ]);
            }

            /**
             * Paycom I
             * ___________________________________________________
             */
            $paycom_i = [
                'payment_reference' => $this->getReferenceFor($this->reference_id),
                'beneficiary_code' => $this->pay_comm_i->account_number,
                'beneficiary_name' => $this->pay_comm_i->code,
                'account_number' => $this->pay_comm_i->account_number,
                'account_type' => 10,
                'cbn_code' => $this->pay_comm_i->bankable->bankCode(),
                'is_cash_card' => '0',
                'narration' => $this->narration,
                'amount' => $this->pay_comm_i_amount,
                'email' => ' ',
                'currency' => 'NGN',
            ];

            $this->sub_mda->autopaySchedules()->create($paycom_i);

            /**
             * Paycom II
             * ___________________________________________________
             */
            $paycom_ii = [
                'payment_reference' => $this->getReferenceFor($this->reference_id),
                'beneficiary_code'  => $this->pay_comm_ii->account_number,
                'beneficiary_name'  => $this->pay_comm_ii->code,
                'account_number'    => $this->pay_comm_ii->account_number,
                'account_type'      => 10,
                'cbn_code'          => $this->pay_comm_ii->bankable->bankCode(),
                'is_cash_card'      => '0',
                'narration'         => $this->narration,
                'amount'            => $this->pay_comm_ii_amount,
                'email'             => ' ',
                'currency'          => 'NGN',
            ];

            $this->sub_mda->autopaySchedules()->create($paycom_ii);

            $this->sub_mda->autopayGenerated();
        }
    }

    private function getReferenceFor($id)
    {
        $month = Str::of($this->month)->limit(3, '');

        $year = Str::of($this->year)->substr(2);

        $unique_id = uniqid();

        return Str::of($this->payment)
                  ->append($month)
                  ->append($year)
                  ->append($id, $unique_id)
                  ->upper();
    }

    private function createNarration($department_name)
    {
        return Str::of($this->reference)
                  ->limit(8, '')
                  ->append($department_name)
                  ->replace(' ', '');
    }

    protected static function pad($string, $padding)
    {
        return str_pad($string, $padding, '0', STR_PAD_LEFT);
    }

    private function initializePayComms() : void
    {
        $pay_comms = $this->domain->payComms;

        $this->pay_comm_i = $pay_comms->where('code', 'PayComm I')->first();
        $this->pay_comm_ii = $pay_comms->where('code', 'PayComm II')->first();
        $this->fidelityLoan = $pay_comms->where('code', 'Fidelity Loan Collection')->first();

        $this->pay_comm_i_charge = $this->pay_comm_i->commission;
        $this->pay_comm_ii_charge = $this->pay_comm_ii->commission;
    }
}
