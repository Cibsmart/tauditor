<?php

namespace App\Http\Controllers;

use App\PaySchedule;
use Inertia\Inertia;
use App\AuditPayroll;
use App\AuditMdaSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use function number_format;

class AuditMdaScheduleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }


    public function index(AuditPayroll $audit_payroll)
    {
        $schedules = $audit_payroll->auditMdaSchedules()
                             ->with(['mda', 'auditPayroll.domain'])
                             ->orderBy('mda_id')
                             ->paginate()
                             ->transform(fn(AuditMdaSchedule $schedule) => [
                                 'id' => $schedule->id,
                                 'payroll_id' => $audit_payroll->id,
                                 'mda_id' => $schedule->mda_id,
                                 'mda_name' => $schedule->mda->name,
                                 'total_amount' => number_format($schedule->total_amount, 2), // 12,000.00
                                 'head_count' => number_format($schedule->head_count), //1,200
                                 'month' => $audit_payroll->month_name,
                                 'year' => $audit_payroll->year,
                                 'uploaded' => $schedule->uploaded,
                                 'domain' => $schedule->auditPayroll->domain->name,
                             ]);

        return Inertia::render('AuditMdaSchedules/Index', [
            'schedules' => $schedules,
        ]);
    }

}
