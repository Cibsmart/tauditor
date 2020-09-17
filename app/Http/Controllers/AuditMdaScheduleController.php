<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\AuditMdaSchedule;
use App\Models\AuditPayrollCategory;

class AuditMdaScheduleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }


    public function index(AuditPayrollCategory $audit_payroll_category)
    {
        if ($audit_payroll_category->domain()->id !== auth()->user()->domain->id) {
            return redirect(route('audit_payroll.index'));
        }

        $schedules = $audit_payroll_category->auditMdaSchedules()->orderBy('mda_name')
                                            ->with(['mda', 'auditPayrollCategory.auditPayroll.domain'])
                                            ->orderBy('mda_id')
                                            ->paginate()
                                            ->transform(fn(AuditMdaSchedule $schedule) => [
                                                'id'           => $schedule->id,
                                                'sub_mda_id'   => $schedule->auditSubMdaSchedules()->first()->id,
                                                'payroll_id'   => $audit_payroll_category->id,
                                                'mda_id'       => $schedule->mda_id,
                                                'mda_name'     => $schedule->mda->name,
                                                'total_amount' => number_format($schedule->total_net_pay, 2),
                                                'head_count'   => number_format($schedule->head_count),
                                                'month'        => $audit_payroll_category->auditPayroll->month_name,
                                                'year'         => $audit_payroll_category->auditPayroll->year,
                                                'uploaded'     => $schedule->uploaded,
                                                'pension'      => $schedule->pension,
                                                'has_sub'      => $schedule->has_sub,
                                                'domain'       => $schedule->auditPayrollCategory->auditPayroll->domain->name,
                                                'archived'     => $this->isArchived($audit_payroll_category),
                                            ]);

        return Inertia::render('AuditMdaSchedules/Index', [
            'schedules' => $schedules,
        ]);
    }

    protected function isArchived(AuditPayrollCategory $audit_payroll_category)
    {
        $now = now();
        $payroll = $audit_payroll_category->auditPayroll;

        return $payroll->month !== $now->month || $payroll->year !== $now->year;
    }
}
