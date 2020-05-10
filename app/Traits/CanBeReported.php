<?php


namespace App\Traits;

use App\AuditReport;

trait CanBeReported
{
    public function report($payroll_category_id, string $category, string $message, $current = null, $previous = null)
    {
        $this->reportable()->firstOrCreate([
            'audit_payroll_category_id' => $payroll_category_id,
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
