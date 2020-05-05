<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\AuditReport;
use App\AuditPayroll;
use Illuminate\Support\Str;
use App\AuditPayrollCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Actions\AuditPayScheduleAction;
use function back;
use function number_format;

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
                            'categories'        => $payroll->auditPaymentCategories->transform(fn ($category) => [
                                'id'              => $category->id,
                                'payment_type_id' => $category->payment_type_id,
                                'payment_type'    => $category->paymentTypeName(),
                                'payment_title'   => $category->payment_title,
                                'total_amount'    => number_format($category->total_net_pay, 2),
                                'head_count'      => number_format($category->head_count),
                            ]),
                        ]);

        return Inertia::render('AuditAnalysis/Index', [
            'payrolls' => $payrolls,
        ]);
    }

    public function analyse(AuditPayrollCategory $audit_payroll_category)
    {
        $mdas = $audit_payroll_category->auditMdaSchedules;
        $title = $audit_payroll_category->payment_title;
        $message = "Analysis Report Generated for $title ";
        $count = 0;

        foreach ($mdas as $mda) {
            $sub_mdas = $mda->auditSubMdaSchedules()->uploaded()->notAnalysed()->get();


            foreach ($sub_mdas as $sub_mda) {
                (new AuditPayScheduleAction)->execute($sub_mda);

                $count++;
            }
        }

        if ($count === 0) {
            $message = "No New Schedule Has Been Uploaded for $title";
            return back()->with('error', $message);
        }

        $mda_string = Str::plural('MDA', $count);

        $message = "$message, $count $mda_string Affected, View Report for Details";

        return back()->with('success', $message);
    }

    public function show(AuditPayrollCategory $audit_payroll_category)
    {
        $reports = $audit_payroll_category->auditReports()
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
