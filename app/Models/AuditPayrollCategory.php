<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @method static find($category)
 * @method static where(string $string, string $string1, $prev_payroll)
 */
class AuditPayrollCategory extends Model
{
    protected $guarded = [];

    public function auditPayroll()
    {
        return $this->belongsTo(AuditPayroll::class);
    }

    public function auditMdaSchedules()
    {
        return $this->hasMany(AuditMdaSchedule::class);
    }

    public function paymentType()
    {
        return $this->belongsTo(PaymentType::class);
    }

    public function auditReports()
    {
        return $this->hasMany(AuditReport::class);
    }

    public function paymentTypeName()
    {
        return $this->paymentType->name;
    }

    public function monthYear($abbreviation = false)
    {
        return $this->auditPayroll->month($abbreviation);
    }

    public function month()
    {
        return $this->auditPayroll->month;
    }

    public function year()
    {
        return $this->auditPayroll->year;
    }

    public function domain()
    {
        return $this->auditPayroll->domain;
    }

    public function mdaCount()
    {
        return $this->auditMdaSchedules->count();
    }

    public function countOfMdasSchedulesUploaded()
    {
        return $this->auditMdaSchedules()
                    ->where('uploaded', '=', 1)
                    ->count();
    }

    public function countOfMdasAnalysed()
    {
        return $this->auditMdaSchedules()
                    ->where('analysed', '=', 1)
                    ->count();
    }

    public function countOfMdasAutopayGenerated()
    {
        return $this->auditMdaSchedules()
                    ->where('autopay_generated', '=', 1)
                    ->count();
    }

    public function setTotalNetPayAttribute(float $value) : int
    {
        return $this->attributes['total_net_pay'] = $value * 100;
    }

    public function getTotalNetPayAttribute(?int $value = 0) : float
    {
        return $value / 100;
    }

    public function auditMdaScheduleWasUpdated()
    {
        $this->total_net_pay = $this->auditMdaSchedules()->sum('total_net_pay') / 100;
        $this->head_count = $this->auditMdaSchedules()->sum('head_count');
        $this->setAnalysisStatus('pending');
        $this->setAutopayStatus('pending');

        $this->save();
    }

    public function analysisIsComplete()
    {
        return $this->auditMdaSchedules()->where('analysed', 0)->doesntExist();
    }

    public function autopayGenerationIsComplete()
    {
        return $this->auditMdaSchedules()->where('autopay_generated', 0)->doesntExist();
    }

    public function analysisIsRunning()
    {
        return $this->auditMdaSchedules()
                    ->where('analysed', 0)
                    ->where('uploaded', 1)
                    ->exists();
    }

    public function autopayGenerationIsRunning()
    {
        return $this->auditMdaSchedules()
                    ->where('autopay_generated', 0)
                    ->where('uploaded', 1)
                    ->exists();
    }

    public function analysisStatusWasUpdated()
    {
        if ($this->analysisIsRunning()) {
            return;
        }

        $status = $this->analysisIsComplete() ? 'completed' : 'incomplete';

        $this->setAnalysisStatus($status);
    }

    public function autopayStatusWasUpdated()
    {
        if ($this->autopayGenerationIsRunning()) {
            return;
        }

        $status = $this->autopayGenerationIsComplete() ? 'completed' : 'incomplete';

        $this->setAutopayStatus($status);
    }

    public function setAnalysisStatus($status)
    {
        $this->analysis_status =  $status;

        $this->save();
    }

    public function setAutopayStatus($status)
    {
        $this->autopay_status  =  $status;

        $this->save();
    }

    public function noAutopaySchedule()
    {
        return $this->auditMdaSchedules()->whereHas('auditSubMdaSchedules', function ($query) {
            return $query->whereNotNull('autopay_generated');
        })->doesntExist();
    }

    public function noMfbSchedule()
    {
        return $this->auditMdaSchedules()->whereHas('auditSubMdaSchedules', function ($query) {
            return $query->whereHas('microfinanceSchedules');
        })->doesntExist();
    }

    public function previousCategory($domain_id)
    {
        $prev_payroll = $this->auditPayroll->previousPayroll($domain_id);

        if (!$prev_payroll) {
            return null;
        }

        return AuditPayrollCategory::where('audit_payroll_id', '=', $prev_payroll->id)
                            ->where('payment_type_id', '=', $this->payment_type_id)
                            ->where('staff_type', '=', $this->staff_type)
                            ->first();
    }
}
