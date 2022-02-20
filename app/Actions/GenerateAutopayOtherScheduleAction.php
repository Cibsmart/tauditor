<?php

namespace App\Actions;

use App\Models\AuditSubMdaSchedule;
use App\Models\FidelityLoanDeduction;
use App\Models\MicroFinanceBank;
use App\Models\OtherAuditPayrollCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GenerateAutopayOtherScheduleAction
{
    protected $year;

    protected $month;

    protected $domain;

    protected $charge;

    protected $payment;

    protected $narration;

    protected $reference;

    protected $reference_id = 1;

    protected $pay_comm_i;

    protected $pay_comm_ii;

    protected $pay_comm_i_amount;

    protected $pay_comm_ii_amount;

    protected $pay_comm_i_charge;

    protected $pay_comm_ii_charge;

    protected const INTERSWITCH_CHARGE = 16.13;

    protected const NYSC_TENECE_CHARGE = 224.25;

    protected const NYSC_FIDELITY_CHARGE = 100.00;

    protected OtherAuditPayrollCategory $category;

    public function execute(OtherAuditPayrollCategory $category)
    {
        $this->category = $category;

        $this->initializePayComms();

        DB::transaction(function () {
            $this->generateAutoPaySchedule();
        });
    }

    private function generateAutoPaySchedule()
    {
        $schedules = $this->category->auditOtherPaySchedules;

        $payroll = $this->category->auditPayroll;

        $this->year = $payroll->year;
        $this->month = $payroll->month_name;
        $this->payment = $this->category->payment_type_id;

        $this->charge = $this->category->paycomm_fidelity || $this->category->paycomm_tenece;

        $this->pay_comm_i_charge = $this->payment === 'nys' ? self::NYSC_FIDELITY_CHARGE : $this->pay_comm_i_charge;
        $this->pay_comm_ii_charge = $this->payment === 'nys' ? self::NYSC_TENECE_CHARGE : $this->pay_comm_ii_charge;

        [$commercial_schedules, $microfinance_schedules] = $schedules->partition(fn (
            $schedule
        ) => $schedule->bankable_type == 'commercial');

        /**
         * Commercial Bank Users
         * ___________________________________________________
         */
        $commercial_users = $commercial_schedules->count();

        $this->pay_comm_i_amount = $this->charge ? $this->pay_comm_i_charge * $commercial_users : 0;
        $this->pay_comm_ii_amount = $this->charge ? $this->pay_comm_ii_charge * $commercial_users : 0;

        foreach ($commercial_schedules as $schedule) {
            $amount = $this->charge
                ? $schedule->amount - $this->pay_comm_i_charge - $this->pay_comm_ii_charge
                : $schedule->amount;

            $this->reference = $this->getReferenceFor($schedule->id);

            $this->narration = $this->createNarration($schedule->narration);

            $attributes = [
                'payment_reference' => $this->reference,
                'beneficiary_code'  => $schedule->account_number,
                'beneficiary_name'  => $schedule->beneficiary_name,
                'account_number'    => $schedule->account_number,
                'account_type'      => 10,
                'cbn_code'          => $schedule->bank_code,
                'is_cash_card'      => '0',
                'narration'         => $this->narration,
                'amount'            => $amount,
                'email'             => ' ',
                'currency'          => 'NGN',
            ];

            $autopay_schedule = $this->category->autopaySchedules()->create($attributes);
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

            $amount = $this->charge
                ? $schedule->amount - $this->pay_comm_i_charge - $this->pay_comm_ii_charge
                : $schedule->amount;

            $this->reference = $this->getReferenceFor($schedule->id);

            $this->narration = $this->createNarration($schedule->narration);

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

            $autopay_schedule = $this->category->microfinanceSchedules()->create($attributes);
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
            $sum_net_pay = $mfb->sum('amount');

            $paycomm_i = $this->charge ? $this->pay_comm_i_charge * $mfb_users : 0;
            $paycomm_ii = $this->charge
                ? ($this->pay_comm_ii_charge + self::INTERSWITCH_CHARGE) * $mfb_users - (self::INTERSWITCH_CHARGE * 2)
                : (self::INTERSWITCH_CHARGE * $mfb_users) - (self::INTERSWITCH_CHARGE * 2);

            $amount = $this->charge
                ? $sum_net_pay - $paycomm_i - $paycomm_ii
                : $sum_net_pay - $paycomm_ii;

            $bank = $schedule->bankable;

            $this->narration = $this->createNarration($schedule->narrations);

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

            $this->category->autopaySchedules()->create($attributes);
        }

        if ($this->narration !== null) {

            /**
             * Paycom I
             * ___________________________________________________
             */
            if ($this->category->paycomm_fidelity) {
                $paycom_i = [
                    'payment_reference' => $this->getReferenceFor($this->reference_id),
                    'beneficiary_code'  => $this->pay_comm_i->account_number,
                    'beneficiary_name'  => $this->pay_comm_i->code,
                    'account_number'    => $this->pay_comm_i->account_number,
                    'account_type'      => 10,
                    'cbn_code'          => $this->pay_comm_i->bankable->bankCode(),
                    'is_cash_card'      => '0',
                    'narration'         => $this->narration,
                    'amount'            => $this->pay_comm_i_amount,
                    'email'             => ' ',
                    'currency'          => 'NGN',
                ];

                $this->category->autopaySchedules()->create($paycom_i);
            } else {
                $this->pay_comm_ii_amount += $this->pay_comm_i_amount;
            }

            /**
             * Paycom II
             * ___________________________________________________
             */
            if ($this->category->paycomm_tenece || $this->pay_comm_ii_amount > 0) {
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

                $this->category->autopaySchedules()->create($paycom_ii);
            }

            $this->category->autopayGenerated();
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

    private function createNarration($description)
    {
        return Str::of($this->reference)
                  ->limit(8, '')
                  ->append($description)
                  ->replace(' ', '');
    }

    protected static function pad($string, $padding)
    {
        return str_pad($string, $padding, '0', STR_PAD_LEFT);
    }

    private function initializePayComms() : void
    {
        $this->domain = $this->category->domain();

        $pay_comms = $this->domain->payComms;

        $this->pay_comm_i = $pay_comms->where('code', 'PayComm I')->first();
        $this->pay_comm_ii = $pay_comms->where('code', 'PayComm II')->first();

        $this->pay_comm_i_charge = $this->pay_comm_i->commission;
        $this->pay_comm_ii_charge = $this->pay_comm_ii->commission;
    }
}
