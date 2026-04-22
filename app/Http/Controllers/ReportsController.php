<?php

namespace App\Http\Controllers;

use App\Models\AuditPayroll;
use App\Models\AuditPayrollCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;

use function number_format;

class ReportsController extends Controller
{
    protected ?AuditPayroll $payroll = null;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function summaryView(Request $request)
    {
        $categories = (object) [];

        $payroll = [
            'id' => '',
            'total_net_pay' => 0,
            'head_count' => 0,
        ];

        $payrolls = Auth::user()->auditPayrolls()
            ->select('id', 'month_name', 'year')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')->get();

        if ($request->has('payroll')) {
            $payroll = AuditPayroll::find($request->payroll);

            $categories = $payroll->auditPaymentCategories()
                ->orderBy('id')
                ->paginate()
                ->transform(fn ($category) => [
                    'id' => $category->id,
                    'payment_title' => $category->payment_title,
                    'total_net_pay' => number_format($category->total_net_pay, 2),
                    'head_count' => number_format($category->head_count),
                ]);
            $payroll = [
                'id' => $payroll->id,
                'total_net_pay' => number_format($payroll->totalNetPay(), 2),
                'head_count' => number_format($payroll->headCount()),
            ];
        }

        return Inertia::render('Reports/SummaryReport', [
            'payrolls' => $payrolls,
            'payroll' => $payroll,
            'categories' => $categories,
        ]);
    }

    public function summaryPrint(AuditPayroll $payroll)
    {
        $categories = $payroll->auditPaymentCategories()
            ->orderBy('id')
            ->get();

        $pdf = App::make('snappy.pdf.wrapper');

        $filename = Str::upper($payroll->month_name . ' ' . $payroll->year . ' - ' . $payroll->domain_id);
        $filename = "PAYMENT SUMMARY - $filename";

        $data = ['categories' => $categories, 'payroll' => $payroll, 'filename' => $filename];

        $pdf->loadView('reports.payment_summary', $data)
            ->setPaper('a4')
            ->setOrientation('portrait')
            ->setOption('dpi', 150)
            ->setOption('footer-center', 'Page [page] of [toPage]')
            ->setOption('footer-font-name', 'san-serif')
            ->setOption('footer-font-size', 8)
            ->setOption('footer-right', '[isodate] [time]')
            ->setOption('footer-left', $filename);

        return $pdf->download($filename . '.pdf');
    }

    public function mdaView(Request $request)
    {
        $reports = (object) [];
        $category = ['id' => ''];

        $payrolls = Auth::user()->auditPayrolls()
            ->with('auditPaymentCategories')
            ->select('id', 'month_name', 'year')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get()
            ->transform(fn (AuditPayroll $payroll) => [
                'id' => $payroll->id,
                'month_name' => $payroll->month_name,
                'year' => $payroll->year,
                'categories' => $payroll->auditPaymentCategories,
            ]);

        if ($request->has('category')) {
            $category = AuditPayrollCategory::find($request->category);

            $previous_category = $category->previousCategory(Auth::user()->domain->id);

            $previous_category_id = $previous_category ? $previous_category->id : 0;

            $month = $category->auditPayroll->month();
            $prev_month = $previous_category ? $previous_category->auditPayroll->month() : 'PREV MONTH';

            $reports = DB::table(
                DB::raw("(select mda_id as mda_id, mda_name as mda_name, count(*) as head_count,
                            sum(basic_pay) as basic_pay, sum(gross_pay) as gross_pay,
                            sum(total_deductions) as total_deduction, sum(net_pay) as net_pay
                            from audit_mda_schedules as mda inner join audit_sub_mda_schedules as sub
                            on sub.audit_mda_schedule_id =  mda.id inner join audit_pay_schedules as pay
                            on pay.audit_sub_mda_schedule_id = sub.id
                            where mda.audit_payroll_category_id = $category->id
                            group by mda_id, mda_name) as curr"),
            )
                ->leftJoin(
                    DB::raw("(select mda_id as prev_mda_id, mda_name as prev_mda_name, count(*) as prev_head_count,
                            sum(basic_pay) as prev_basic_pay, sum(gross_pay) as prev_gross_pay,
                            sum(total_deductions) as prev_total_deduction, sum(net_pay) as prev_net_pay
                            from audit_mda_schedules as mda inner join audit_sub_mda_schedules as sub
                            on sub.audit_mda_schedule_id =  mda.id inner join audit_pay_schedules as pay
                            on pay.audit_sub_mda_schedule_id = sub.id
                            where mda.audit_payroll_category_id = $previous_category_id
                            group by mda_id, mda_name) as prev"),
                    'prev.prev_mda_id',
                    '=',
                    'curr.mda_id',
                )
                ->paginate(30)
                ->transform(fn ($report) => [
                    'mda_id' => $report->mda_id,
                    'mda_name' => $report->mda_name,

                    'month' => $month,
                    'head_count' => $this->formatNumber($report->head_count),
                    'basic_pay' => $this->formatAmount($report->basic_pay),
                    'gross_pay' => $this->formatAmount($report->gross_pay),
                    'deduction' => $this->formatAmount($report->total_deduction),
                    'net_pay' => $this->formatAmount($report->net_pay),

                    'prev_month' => $prev_month,
                    'prev_head_count' => $this->formatNumber($report->prev_head_count),
                    'prev_basic_pay' => $this->formatAmount($report->prev_basic_pay),
                    'prev_gross_pay' => $this->formatAmount($report->prev_gross_pay),
                    'prev_deduction' => $this->formatAmount($report->prev_total_deduction),
                    'prev_net_pay' => $this->formatAmount($report->prev_net_pay),

                    'diff_head_count' => $this->formatNumber($report->head_count - $report->prev_head_count),
                    'diff_basic_pay' => $this->formatAmount($report->basic_pay - $report->prev_basic_pay),
                    'diff_gross_pay' => $this->formatAmount($report->gross_pay - $report->prev_gross_pay),
                    'diff_deduction' => $this->formatAmount($report->total_deduction - $report->prev_total_deduction),
                    'diff_net_pay' => $this->formatAmount($report->net_pay - $report->prev_net_pay),
                ]);

            $category = ['id' => $category->id];
        }

        return Inertia::render('Reports/MdaReport', [
            'reports' => $reports,
            'category' => $category,
            'payrolls' => $payrolls,
        ]);
    }

    public function mdaPrint(AuditPayrollCategory $category)
    {
        $previous_category = $category->previousCategory(Auth::user()->domain->id);

        $previous_category_id = $previous_category ? $previous_category->id : 0;

        $month = $category->auditPayroll->month();
        $prev_month = $previous_category ? $previous_category->auditPayroll->month() : 'PREV MONTH';

        $reports = DB::table(
            DB::raw("(select mda_id as mda_id, mda_name as mda_name, count(*) as head_count,
                            sum(basic_pay) as basic_pay, sum(gross_pay) as gross_pay,
                            sum(total_deduction) as total_deduction, sum(net_pay) as net_pay
                            from audit_mda_schedules as mda inner join audit_sub_mda_schedules as sub
                            on sub.audit_mda_schedule_id =  mda.id inner join audit_pay_schedules as pay
                            on pay.audit_sub_mda_schedule_id = sub.id
                            where mda.audit_payroll_category_id = $category->id
                            group by mda_id, mda_name) as curr"),
        )
            ->leftJoin(
                DB::raw("(select mda_id as prev_mda_id, mda_name as prev_mda_name, count(*) as prev_head_count,
                            sum(basic_pay) as prev_basic_pay, sum(gross_pay) as prev_gross_pay,
                            sum(total_deduction) as prev_total_deduction, sum(net_pay) as prev_net_pay
                            from audit_mda_schedules as mda inner join audit_sub_mda_schedules as sub
                            on sub.audit_mda_schedule_id =  mda.id inner join audit_pay_schedules as pay
                            on pay.audit_sub_mda_schedule_id = sub.id
                            where mda.audit_payroll_category_id = $previous_category_id
                            group by mda_id, mda_name) as prev"),
                'prev.prev_mda_id',
                '=',
                'curr.mda_id',
            )->get();

        $pdf = App::make('snappy.pdf.wrapper');

        $filename = Str::upper($category->payment_title);
        $filename = "MDA-ZONE ANALYSIS REPORT - $filename";

        $data = ['reports' => $reports, 'month' => $month, 'prev_month' => $prev_month, 'filename' => $filename];

        $pdf->loadView('reports.mda_analysis', $data)
            ->setPaper('a4')
            ->setOrientation('landscape')
            ->setOption('dpi', 150)
            ->setOption('footer-center', 'Page [page] of [toPage]')
            ->setOption('footer-font-name', 'san-serif')
            ->setOption('footer-font-size', 8)
            ->setOption('footer-right', '[isodate] [time]')
            ->setOption('footer-left', $filename);

        return $pdf->download($filename . '.pdf');
    }

    protected function formatAmount($value)
    {
        return number_format($value / 100, 2);
    }

    protected function formatNumber($value)
    {
        return number_format($value);
    }
}
