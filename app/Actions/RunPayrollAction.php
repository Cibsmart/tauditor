<?php


namespace App\Actions;


use App\Payroll;
use App\Beneficiary;
use App\PaySchedule;

class RunPayrollAction
{
    public function execute(Payroll $payroll)
    {
        $this->runPayrollFor($payroll);
    }

    private function runPayrollFor(Payroll $payroll)
    {
        //Get a list of all active beneficiary
        $active_beneficiaries = $payroll->domain->beneficiaries()
                                                ->active()
                                                ->cursor();
        //For each beneficiary
        foreach ($active_beneficiaries as $beneficiary)
        {
            $basic_pay = $beneficiary->basic();
            $total_allowance = $beneficiary->totalMonthlyAllowance();
            $total_deduction = $beneficiary->totalMonthlyDeduction();

            $gross_pay = $basic_pay + $total_allowance;
            $net_pay = $gross_pay - $total_deduction;

            $mda = $beneficiary->mdaDetail;

            $bank = $beneficiary->bankDetail;

            $payable = $beneficiary->salaryDetail;

            $pay_schedule = [
                'beneficiary_code' => $bank->account_number,
                'beneficiary_name' => $beneficiary->name,
                'account_number' => $bank->account_number,
                'bank_name' => $bank->bankable->name,
                'net_pay' => $net_pay,
                'basic_pay' => $basic_pay,
                'gross_pay' => $gross_pay,
                'total_allowance' => $total_allowance,
                'total_deduction' => $total_deduction,
                'allowances' => $beneficiary->allowances(),
                'deductions' => $beneficiary->deductions(),

                'mda_name' => $mda->mda->name,
                'sub_mda_name' => $mda->subMda->name,
                'sub_sub_mda_name' => $mda->subSubMda->name,

                'beneficiary_id' => $beneficiary->id,
                'verification_number' => $beneficiary->id, //TODO: Should be updated to $beneficiary->verification_number
                'beneficiary_type_id' => $beneficiary->beneficiary_type_id,
                'bankable_type' => $bank->bankable_type,
                'bankable_id' => $bank->bankable_id,
                'payable_type' => $payable->payable_type,
                'payable_id' => $payable->payable_id,

                'mda_id' => $mda->mda_id,
                'sub_mda_id' => $mda->sub_mda_id,
                'sub_sub_mda_id' => $mda->sub_sub_mda_id,

                'pensioner' => $beneficiary->pensioner,
            ];

            $payroll->schedules()->create($pay_schedule);
        }
    }
}
