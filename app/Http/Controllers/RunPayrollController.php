<?php

namespace App\Http\Controllers;

use App\Payroll;
use App\Actions\RunPayrollAction;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class RunPayrollController
{
    use AuthorizesRequests;

    public function __invoke(Payroll $payroll, RunPayrollAction $run_payroll_action)
    {
        $this->authorize('payrun', Payroll::class);

        $run_payroll_action->execute($payroll);

        return redirect()->route('payroll.index')
                         ->with('success', "Successful Payroll Run for $payroll->month_name $payroll->year");
    }
}
