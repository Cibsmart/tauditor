<?php


namespace App\Actions;


use App\Payroll;
use function redirect;

class RunPayrollAction
{
    public function execute(Payroll $payroll)
    {
        $this->runPayroll($payroll);


    }

    private function runPayroll(Payroll $payroll)
    {
        //Get a list of all active beneficiary

        //For each beneficiary
        //1. Basic Pay
        //2. Allowances
        //3. Deduction

        //Then save in pay_schedules table
    }
}
