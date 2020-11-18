<?php


namespace App\Traits;

use App\Models\AuditReport;
use function is_null;

trait CanBeReported
{
    public function report(
        $payroll_category_id,
        $audit_sub_mad_schedule_id,
        $category,
        $message,
        $current = null,
        $previous = null
    ) {
        $attributes = [
            'audit_payroll_category_id' => $payroll_category_id,
            'audit_sub_mda_schedule_id' => $audit_sub_mad_schedule_id,
            'category'                  => $category,
            'message'                   => $message,
            'current_value'             => $current,
            'previous_value'            => $previous,
        ];

        $report = AuditReport::query()->where('reportable_type', $this->getMorphClass())
                             ->where('reportable_id', $this->getKey())
                             ->where('audit_payroll_category_id', $payroll_category_id)
                             ->where('audit_sub_mda_schedule_id', $audit_sub_mad_schedule_id)
                             ->where('category', $category)
                             ->get();

        if (is_null($report)) {
            $this->reportable()->create($attributes);
        }
    }

    public function reportable()
    {
        return $this->morphOne(AuditReport::class, 'reportable');
    }
}
