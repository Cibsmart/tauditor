<?php

namespace App\Http\Controllers;

use App\Actions\AuditPayScheduleAction;
use App\Jobs\AnalysePaySchedules;
use App\Models\AuditPayroll;
use App\Models\AuditPayrollCategory;
use App\Models\AuditReport;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;

use function back;

class AuditAnalysisController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $payrolls = Auth::user()->auditPayrolls()
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->paginate()
            ->transform(fn (AuditPayroll $payroll) => [
                'id' => $payroll->id,
                'month' => $payroll->month_name,
                'year' => $payroll->year,
                'created_by' => $payroll->createdBy(),
                'date_created' => $payroll->dateCreated(),
                'autopay_generated' => $payroll->autopay_generated,
                'categories' => $payroll->auditPaymentCategories
                    ->transform(function ($category) {
                        $uploaded_count = $category->countOfMdasSchedulesUploaded();
                        $analysed_count = $category->countOfMdasAnalysed();
                        $status = $category->analysis_status;
                        $available = $uploaded_count - $analysed_count > 0;

                        return [
                            'id' => $category->id,
                            'payment_type_id' => $category->payment_type_id,
                            'payment_type' => $category->paymentTypeName(),
                            'payment_title' => $category->payment_title,
                            'analysis_status' => $status,
                            'mda_count' => $category->mdaCount(),
                            'uploaded_count' => $uploaded_count,
                            'analysed_count' => $analysed_count,
                            'analysable' => $available && $status !== 'running',
                            'viewable' => $analysed_count > 0,
                            'refreshable' => $available && $status === 'running',
                        ];
                    }),
            ]);

        return Inertia::render('AuditAnalysis/Index', [
            'payrolls' => $payrolls,
        ]);
    }

    public function pdfReport(AuditPayrollCategory $audit_payroll_category)
    {
        $reports = $audit_payroll_category->auditReports()
            ->select(DB::raw('reportable_type, reportable_id'))
            ->groupBy('reportable_type', 'reportable_id')
            ->where('reportable_type', 'audit_pay_schedule')
            ->orderBy('reportable_id')
            ->get();

        $data = ['reports' => $reports, 'category' => $audit_payroll_category];

        $pdf = App::make('snappy.pdf.wrapper');

        $pdf->loadView('reports.analysis_report', $data)
            ->setPaper('a4')
            ->setOrientation('landscape')
            ->setOption('dpi', 150)
            ->setOption('footer-center', 'Page [page] of [toPage]')
            ->setOption('footer-font-name', 'san-serif')
            ->setOption('footer-font-size', 8)
            ->setOption('footer-right', '[isodate] [time]')
            ->setOption('footer-left', $audit_payroll_category->payment_title);

        return $pdf->download('ANALYSIS REPORT - '.$audit_payroll_category->payment_title.'.pdf');
    }

    public function analyse(AuditPayrollCategory $audit_payroll_category)
    {
        $mdas = $audit_payroll_category->auditMdaSchedules;
        $title = $audit_payroll_category->payment_title;
        $count = 0;

        if ($audit_payroll_category->analysis_status !== 'pending') {
            $message = "No [New] Schedule Has Been Uploaded for $title";

            return back()->with('error', $message);
        }

        $audit_payroll_category->setAnalysisStatus('running');

        foreach ($mdas as $mda) {
            $sub_mdas = $mda->auditSubMdaSchedules()->uploaded()->notAnalysed()->get();

            foreach ($sub_mdas as $sub_mda) {
                AnalysePaySchedules::dispatch($sub_mda);
                //                (new AuditPayScheduleAction)->execute($sub_mda); //Run Synchronously
                $count++;
            }
        }

        $mda_string = Str::plural('MDA', $count);

        $message = "Analysis Report for $count $mda_string in $title is Running, Refresh for Update";

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
                'reports' => $report->reportable->auditReports->map(fn (
                    $rep
                ) => $rep->only(
                    'id',
                    'message',
                    'current_value',
                    'previous_value'
                )),
            ]);

        return Inertia::render('AuditAnalysis/Show', [
            'reports' => $reports,
            'audit_payroll_category' => $audit_payroll_category->id,
        ]);
    }
}
