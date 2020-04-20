<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\AuditPayroll;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Actions\AuditPayScheduleAction;
use App\Actions\GenerateAutoPayScheduleAction;
use function back;

class AuditAnalysisController extends Controller
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

        return Inertia::render('AuditAnalysis/Index', [
            'payrolls' => $payrolls,
        ]);
    }

    public function analyse(AuditPayroll $audit_payroll)
    {
        $mdas = $audit_payroll->auditMdaSchedules;
        $message = 'Analysis Report Generated for ';
        $count = 0;

        foreach ($mdas as $mda) {
            $sub_mdas = $mda->auditSubMdaSchedules()->uploaded()->notAnalysed()->get();

            foreach ($sub_mdas as $sub_mda) {

                (new AuditPayScheduleAction)->execute($sub_mda);

                $count++;
            }
        }

        $message = "$message $count MDAs, View Report for Details";

        return back()->with('success', $message);
    }
}
