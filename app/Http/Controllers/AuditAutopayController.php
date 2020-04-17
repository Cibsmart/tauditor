<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\AuditPayroll;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Actions\GenerateAutoPayScheduleAction;

class AuditAutopayController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $payrolls = Auth::user()->auditPayrolls()->latest()
                        ->paginate()
                        ->transform(fn(AuditPayroll $payroll) => [
                            'id' => $payroll->id,
                            'month' => $payroll->month_name,
                            'year' => $payroll->year,
                            'created_by' => $payroll->createdBy(),
                            'date_created' => $payroll->dateCreated(),
                            'autopay_generated' => $payroll->autopay_generated,
                        ]);

        return Inertia::render('AuditAutoPay/Index', [
            'payrolls' => $payrolls,
        ]);
    }

    public function generate(AuditPayroll $audit_payroll)
    {
        $mdas = $audit_payroll->auditMdaSchedules;

        foreach ($mdas as $mda) {
            $sub_mdas = $mda->auditSubMdaSchedules()->uploaded()->autopayNotGenerated()->get();

            foreach ($sub_mdas as $sub_mda) {
                (new GenerateAutoPayScheduleAction())->execute($sub_mda);
            }
        }
    }
}
