<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\AuditMdaSchedule;
use App\Models\AuditSubMdaSchedule;

class AuditSubMdaSchedulesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }


    /**
     * @param  AuditMdaSchedule  $audit_mda_schedule
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Inertia\Response
     */
    public function index(AuditMdaSchedule $audit_mda_schedule)
    {
        if ($audit_mda_schedule->domain()->id !== auth()->user()->domain->id) {
            return redirect(route('audit_payroll.index'));
        }

        $schedules = $audit_mda_schedule->auditSubMdaSchedules()->orderBy('sub_mda_name')
                                        ->with('auditMdaSchedule.auditPayrollCategory.auditPayroll')
                                        ->paginate()
                                        ->transform(fn(AuditSubMdaSchedule $schedule) => [
                                            'id'           => $schedule->id,
                                            'sub_mda_name' => $schedule->sub_mda_name,
                                            'total_amount' => number_format($schedule->total_net_pay, 2), // 12,000.00
                                            'head_count'   => number_format($schedule->head_count), //1,200
                                            'month'        => $audit_mda_schedule->auditPayrollCategory->auditPayroll->month_name,
                                            'year'         => $audit_mda_schedule->auditPayrollCategory->auditPayroll->year,
                                            'uploaded'     => $schedule->uploaded,
                                            'mda_name'     => $audit_mda_schedule->mda_name,
                                            'archived'     => $this->isArchived($audit_mda_schedule),
                                        ]);

        return Inertia::render('AuditSubMdaSchedules/Index', [
            'schedules'              => $schedules,
            'audit_payroll_category' => $audit_mda_schedule->auditPayrollCategory->id,
        ]);
    }

    protected function isArchived(AuditMdaSchedule $audit_mda_schedule)
    {
        $now = now();
        $payroll = $audit_mda_schedule->auditPayrollCategory->auditPayroll;

        return $payroll->month !== $now->month || $payroll->year !== $now->year;
    }
}
