<?php


namespace App\Traits;


use App\AuditReport;

trait CanBeReported
{
    public function report($payroll_id, string $category, string $content)
    {
        $this->reportable()->firstOrCreate([
            'category' => $category,
            'content' => $content,
            'audit_payroll_id' => $payroll_id,
        ]);
    }

    public function reportable()
    {
        return $this->morphOne(AuditReport::class, 'reportable');
    }
}
