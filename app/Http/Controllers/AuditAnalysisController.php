<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\PaySchedule;
use App\AuditPayroll;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Actions\AuditPayScheduleAction;
use App\Actions\GenerateAutoPayScheduleAction;
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

        if($count === 0){
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
        $reports = $audit_payroll->schedules()
                             ->with(['mda', 'payroll.domain'])
                             ->select(DB::raw('count(*) as head_count, sum(net_pay) as total_amount, mda_id, payroll_id, pensioner')) //total_amount get attribute in PaySchedule
                             ->groupBy('mda_id', 'payroll_id', 'pensioner')
                             ->orderBy('mda_id')
                             ->paginate()
                             ->transform(fn(PaySchedule $schedule) => [
                                 'payroll_id' => $audit_payroll->id,
                                 'mda_id' => $schedule->mda->id,
                                 'mda_name' => $schedule->mda->name,
                                 'total_amount' => number_format($schedule->total_amount, 2), // 12,000.00
                                 'head_count' => number_format($schedule->head_count), //1,200
                                 'month' => $audit_payroll->month_name,
                                 'year' => $audit_payroll->year,
                                 'domain' => $schedule->payroll->domain->name,
                                 'pensioner' => $schedule->pensioner,
                             ]);

        dd($reports);

        return Inertia::render('AuditAnalysis/Show', [
            'reports' => $reports,
        ]);
    }
}
