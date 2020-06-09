<?php


namespace App\Traits;

use App\AuditReport;

trait CanBeReported
{
    public function report($payroll_category_id, $audit_sub_mad_schedule_id, string $category, string $message, $current = null, $previous = null)
    {
        $this->reportable()->firstOrCreate([
            'audit_payroll_category_id' => $payroll_category_id,
            'audit_sub_mda_schedule_id' => $audit_sub_mad_schedule_id,
            'category'         => $category,
            'message'          => $message,
            'current_value'    => $current,
            'previous_value'   => $previous,
        ]);
    }

    public function reportable()
    {
        return $this->morphOne(AuditReport::class, 'reportable');
    }
}
