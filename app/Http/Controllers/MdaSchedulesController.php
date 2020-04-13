<?php

namespace App\Http\Controllers;

use App\Payroll;
use Inertia\Inertia;
use App\PaySchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use function back;
use function number_format;

class MdaSchedulesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Payroll $payroll)
    {
        if(! $payroll->generated){
            return back()->withError('Yet to Run Payroll for $date->monthName $date->year"');
        }

        $schedules = $payroll->schedules()
                             ->with(['mda', 'payroll.domain'])
                             ->select(DB::raw('count(*) as head_count, sum(net_pay) as total_amount, mda_id, payroll_id, pensioner')) //total_amount get attribute in PaySchedule
                             ->groupBy('mda_id', 'payroll_id', 'pensioner')
                             ->orderBy('mda_id')
                             ->paginate()
                             ->transform(fn(PaySchedule $schedule) => [
                                 'payroll_id' => $payroll->id,
                                 'mda_id' => $schedule->mda->id,
                                 'mda_name' => $schedule->mda->name,
                                 'total_amount' => number_format($schedule->total_amount, 2), // 12,000.00
                                 'head_count' => number_format($schedule->head_count), //1,200
                                 'month' => $payroll->month_name,
                                 'year' => $payroll->year,
                                 'domain' => $schedule->payroll->domain->name,
                                 'pensioner' => $schedule->pensioner,
                             ]);

        return Inertia::render('MdaSchedules/Index', [
            'schedules' => $schedules,
        ]);
    }
}
