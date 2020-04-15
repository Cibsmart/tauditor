<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\AuditPayroll;
use App\AuditMdaSchedule;
use Illuminate\Http\Request;
use App\AuditSubMdaSchedules;

class AuditSubMdaSchedulesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }


    public function index(AuditMdaSchedule $audit_mda_schedule)
    {
        $schedules = $audit_mda_schedule->auditSubMdaSchedules()
                                        ->with('auditMdaSchedule.auditPayroll')
                                        ->paginate()
                                        ->transform(fn(AuditSubMdaSchedules $schedule) => [
                                           'id' => $schedule->id,
                                           'sub_mda_name' => $schedule->sub_mda_name,
                                           'total_amount' => number_format($schedule->total_net_amount, 2), // 12,000.00
                                           'head_count' => number_format($schedule->head_count), //1,200
                                           'month' => $audit_mda_schedule->auditPayroll->month_name,
                                           'year' => $audit_mda_schedule->auditPayroll->year,
                                           'uploaded' => $schedule->uploaded,
                                           'mda_name' => $audit_mda_schedule->mda_name,
                                       ]);

        return Inertia::render('AuditSubMdaSchedules/Index', [
            'schedules' => $schedules,
            'audit_payroll' => $audit_mda_schedule->auditPayroll->id,
        ]);
    }
}
