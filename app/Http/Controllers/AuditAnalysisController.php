<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\AuditReport;
use App\AuditPayroll;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Actions\AuditPayScheduleAction;
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
                        ->transform(fn (AuditPayroll $payroll) => [
                            'id'                => $payroll->id,
                            'month'             => $payroll->month_name,
                            'year'              => $payroll->year,
                            'created_by'        => $payroll->createdBy(),
                            'date_created'      => $payroll->dateCreated(),
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

        if ($count === 0) {
            $month = $audit_payroll->month_name;
            $year = $audit_payroll->year;
            $message = "Not Schedule Has Been Uploaded for $month $year";
            return back()->with('error', $message);
        }

        $message = "$message $count MDAs, View Report for Details";

        return back()->with('success', $message);
    }

    public function show(AuditPayroll $audit_payroll)
    {
        $reports = $audit_payroll->auditReports()
                                 ->select(DB::raw('reportable_type, reportable_id'))
                                 ->groupBy('reportable_type', 'reportable_id')
                                 ->where('reportable_type', 'audit_pay_schedule')
                                 ->paginate()
                                 ->transform(fn (AuditReport $report) => [
                                     'schedule' => $report->reportable->only(
                                         'beneficiary_name',
                                         'verification_number',
                                         'pension'
                                     ),
                                     'reports'  => $report->reportable->auditReports->map(fn ($rep) => $rep->only(
                                         'id',
                                         'message',
                                         'current_value',
                                         'previous_value'
                                     )),
                                 ]);

        return Inertia::render('AuditAnalysis/Show', [
            'reports' => $reports,
        ]);
    }
}
