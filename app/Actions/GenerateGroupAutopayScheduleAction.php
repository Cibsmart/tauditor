<?php

namespace App\Actions;

use App\Models\AuditMdaSchedule;
use App\Models\AuditPayrollCategory;
use App\Models\AuditPaySchedule;
use App\Models\AuditSubMdaSchedule;
use App\Models\AutopaySchedule;
use App\Models\BeneficiaryType;
use App\Models\Domain;
use App\Models\FidelityLoanDeduction;
use App\Models\FidelityLoanSchedule;
use App\Models\MicroFinanceBank;
use App\Models\MicrofinanceBankSchedule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GenerateGroupAutopayScheduleAction
{
    protected $year;

    protected $month;

    protected $domain;

    protected $payment;

    protected $narration;

    protected $reference;

    protected $referenceId = 1;

    protected $payCommI;

    protected $payCommII;

    protected $fidelityLoan;

    protected $payCommIAmount;

    protected $payCommIIAmount;

    protected $payCommICharge;

    protected $payCommIICharge;

    protected const INTERSWITCH_CHARGE = 16.13;

    public AuditSubMdaSchedule $subMda;

    public AuditPayrollCategory $category;

    public BeneficiaryType $beneficiaryType;

    public function execute(Domain $domain, AuditPayrollCategory $category, BeneficiaryType $beneficiaryType)
    {
        $this->domain = $domain;
        $this->category = $category;
        $this->beneficiaryType = $beneficiaryType;

        $this->initializePayComms();


        DB::transaction(function () {
            $this->generateAutoPaySchedule();
        });
    }


    private function initializePayComms() : void
    {
        $payComms = $this->domain->payComms;

        $this->payCommI = $payComms->where('code', 'PayComm I')->first();
        $this->payCommII = $payComms->where('code', 'PayComm II')->first();
        $this->fidelityLoan = $payComms->where('code', 'Fidelity Loan Collection')->first();

        $this->payCommICharge = $this->payCommI->commission;
        $this->payCommIICharge = $this->payCommII->commission;
    }

    private function generateAutoPaySchedule()
    {
        $query = AuditPaySchedule::query()
            ->select(
                'audit_pay_schedules.id',
                'month',
                'bankable_type',
                'bankable_id',
                'net_pay',
                'verification_number',
                'account_number',
                'beneficiary_name',
                'audit_sub_mda_schedule_id'
            )
            ->join('audit_sub_mda_schedules', 'audit_pay_schedules.audit_sub_mda_schedule_id', '=', 'audit_sub_mda_schedules.id')
            ->join('audit_mda_schedules', 'audit_sub_mda_schedules.audit_mda_schedule_id', '=', 'audit_mda_schedules.id')
            ->join('mdas', 'audit_mda_schedules.mda_id', '=', 'mdas.id')
            ->where('audit_payroll_category_id', $this->category->id)
            ->where('beneficiary_type_id', $this->beneficiaryType->id)
            ->orderBy('audit_sub_mda_schedules.sub_mda_name');

        dd($schedules);
        $schedules = $query->get();

        $schedule = $schedules->first();

        $this->year = $schedule->month->year;
        $this->month = $schedule->month->monthName;
        $this->payment = Str::upper($this->category->payment_type_id);

        [$commercialSchedules, $microfinanceSchedules] = $schedules->partition(fn (
            $schedule
        ) => $schedule->bankable_type == 'commercial');

        /**
         * Commercial Bank Users
         * ___________________________________________________
         */
        $commercialUsers = $commercialSchedules->count();

        $this->payCommIAmount = $this->payCommICharge * $commercialUsers;
        $this->payCommIIAmount = $this->payCommIICharge * $commercialUsers;

        foreach ($commercialSchedules as $schedule) {
            $amount = $schedule->net_pay - $this->payCommICharge - $this->payCommIICharge;
            $this->subMda = AuditSubMdaSchedule::find($schedule->audit_sub_mda_id);

            if ($schedule->loan->count() > 0) {
                $loans = $schedule->loan->where('status', 'A');

                foreach ($loans as $loan) {
                    if ($loan->isNotPaid()) {
                        $loanAmount = $loan->collection_amount;

                        $amount = $amount - $loanAmount - self::INTERSWITCH_CHARGE;
                        $this->payCommIIAmount += self::INTERSWITCH_CHARGE;

                        $loan->deductions()->create([
                            'amount'                    => $loanAmount,
                            'loan_account'              => $loan->account_number,
                            'audit_sub_mda_schedule_id' => $schedule->audit_sub_mda_schedule_id,
                        ]);
                    }

                    if ($loan->isPaid()) {
                        $loan->markAsPaid();
                    }
                }
            }

            $this->reference = $this->getReferenceFor($schedule->id);

            if (! $this->narration) {
                $this->narration = $this->createNarration($this->subMda->sub_mda_name);
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

            $autopaySchedule = $this->subMda->autopaySchedules()->create($attributes);
            $schedule->autopay_schedule_id = $autopaySchedule->id;
            $schedule->save();
        }

        $ignore = MicroFinanceBank::where('name', '=', 'CASH PAYMENT')->first();

        /**
         * Microfinance Bank Users
         * ___________________________________________________
         */
        foreach ($microfinanceSchedules as $schedule) {
            if ($schedule->bankable_id == $ignore->id) {
                continue;
            }

            $amount = $schedule->net_pay - $this->payCommICharge - $this->payCommIICharge - self::INTERSWITCH_CHARGE;

            $this->reference = $this->getReferenceFor($schedule->id);

            if (! $this->narration) {
                $this->narration = $this->createNarration($this->subMda->sub_mda_name);
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

            $autopay_schedule = $this->subMda->microfinanceSchedules()->create($attributes);
            $schedule->autopay_schedule_id = $autopay_schedule->id;
            $schedule->save();
        }

        $mfbs = $microfinanceSchedules->groupBy('bankable_id');

        foreach ($mfbs as $mfb) {
            $schedule = $mfb->first();

            if ($schedule->bankable_id == $ignore->id) {
                continue;
            }

            $mfbUsers = $mfb->count();
            $sumNetPay = $mfb->sum('net_pay');

            $payCommI = $this->payCommICharge * $mfbUsers;
            $payCommII = ($this->payCommIICharge + self::INTERSWITCH_CHARGE) * $mfbUsers
                - self::INTERSWITCH_CHARGE;

            $amount = $sumNetPay - $payCommI - $payCommII;

            $bank = $schedule->bankable;

            if (! $this->narration) {
                $this->narration = $this->createNarration($this->subMda->sub_mda_name);
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

            $this->payCommIAmount = $this->payCommIAmount + $payCommI;
            $this->payCommIIAmount = $this->payCommIIAmount + $payCommII;

            $this->subMda->autopaySchedules()->create($attributes);
        }

        if ($this->narration !== null) {

            /**
             * Fidelity Loan
             * ___________________________________________________
             */
            $subMdas = FidelityLoanDeduction::query()
                ->where('beneficiary_type_id', $this->beneficiaryType->id)
                ->whereIn('audit_sub_mda_schedule_id', $query->pluck('audit_sub_mda_schedule_id'));

            if ($subMdas->count() > 0) {
                $fidelityLoanAmount = $subMdas->sum('amount') / 100 + self::INTERSWITCH_CHARGE;
                $this->payCommIIAmount -= self::INTERSWITCH_CHARGE;

                $fidelityLoan = [
                    'payment_reference' => $this->getReferenceFor($this->referenceId),
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

                $this->subMda->autopaySchedules()->create($fidelityLoan);

                $fidelitySchedule = $this->subMda->fidelitySchedules->create($fidelityLoan);

                FidelityLoanDeduction::where('audit_sub_mda_schedule_id', $schedule->audit_sub_mda_schedule_id)
                    ->update(['fidelity_loan_schedule_id' => $fidelitySchedule->id]);
            }

            /**
             * Paycom I
             * ___________________________________________________
             */
            $paycomI = [
                'payment_reference' => $this->getReferenceFor($this->referenceId),
                'beneficiary_code'  => $this->payCommI->account_number,
                'beneficiary_name'  => $this->payCommI->code,
                'account_number'    => $this->payCommI->account_number,
                'account_type'      => 10,
                'cbn_code'          => $this->payCommI->bankable->bankCode(),
                'is_cash_card'      => '0',
                'narration'         => $this->narration,
                'amount'            => $this->payCommIAmount,
                'email'             => ' ',
                'currency'          => 'NGN',
            ];

            $this->subMda->autopaySchedules()->create($paycomI);

            /**
             * Paycom II
             * ___________________________________________________
             */
            $paycomII = [
                'payment_reference' => $this->getReferenceFor($this->referenceId),
                'beneficiary_code'  => $this->payCommII->account_number,
                'beneficiary_name'  => $this->payCommII->code,
                'account_number'    => $this->payCommII->account_number,
                'account_type'      => 10,
                'cbn_code'          => $this->payCommII->bankable->bankCode(),
                'is_cash_card'      => '0',
                'narration'         => $this->narration,
                'amount'            => $this->payCommIIAmount,
                'email'             => ' ',
                'currency'          => 'NGN',
            ];

            $this->subMda->autopaySchedules->create($paycomII);

            $subMdas = AuditSubMdaSchedule::query()
                ->whereIn('id', $query->pluck('audit_sub_mda_schedule_id'))
                ->get();

            foreach ($subMdas as $subMda) {
                $subMda->autopayGenerated();
            }
        }
    }

    private function createNarration($department_name)
    {
        return Str::of($this->reference)
            ->limit(8, '')
            ->append($department_name)
            ->replace(' ', '');
    }

    private function getReferenceFor($id)
    {
        $month = Str::of($this->month)->limit(3, '');

        $year = Str::of($this->year)->substr(2);

        $uniqueId = uniqid();

        return Str::of($this->payment)
            ->append($month)
            ->append($year)
            ->append($id, $uniqueId)
            ->upper();
    }

    protected static function pad($string, $padding)
    {
        return str_pad($string, $padding, '0', STR_PAD_LEFT);
    }
}
