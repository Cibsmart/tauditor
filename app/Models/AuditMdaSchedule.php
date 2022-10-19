<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AuditMdaSchedule extends Model
{
    use SoftDeletes;

    protected $perPage = 35;

    protected $guarded = [];

    protected $casts = [
        'analysed' => 'boolean',
        'has_sub'  => 'boolean',
        'pension'  => 'boolean',
        'uploaded' => 'boolean',
        'autopay_generated' => 'boolean',
        'autopay_uploaded' => 'boolean',
    ];

    public function mda()
    {
        return $this->belongsTo(Mda::class);
    }

    public function auditPayrollCategory()
    {
        return $this->belongsTo(AuditPayrollCategory::class);
    }

    public function auditSubMdaSchedules()
    {
        return $this->hasMany(AuditSubMdaSchedule::class);
    }

    public function auditPaySchedules()
    {
        return $this->hasManyThrough(AuditPaySchedule::class, AuditSubMdaSchedule::class);
    }

    public function domain()
    {
        return $this->auditPayrollCategory->auditPayroll->domain;
    }

    public function paymentCredential()
    {
        return $this->mda->beneficiaryType->paymentCredential;
    }

    public function scopeMfbSchedules($query)
    {
        return $query->join('mdas', 'audit_mda_schedules.mda_id', '=', 'mdas.id')
                     ->join('audit_sub_mda_schedules', 'audit_mda_schedules.id', '=', 'audit_sub_mda_schedules.audit_mda_schedule_id')
                     ->join('microfinance_bank_schedules', 'audit_sub_mda_schedules.id', '=', 'microfinance_bank_schedules.audit_sub_mda_schedule_id');
    }

    public function scopePaySchedules($query)
    {
        return $query->join('mdas', 'audit_mda_schedules.mda_id', '=', 'mdas.id')
            ->join('audit_sub_mda_schedules', 'audit_mda_schedules.id', '=', 'audit_sub_mda_schedules.audit_mda_schedule_id')
            ->join('audit_pay_schedules', 'audit_sub_mda_schedules.id', '=', 'audit_pay_schedules.audit_sub_mda_schedule_id');
    }

    public function setTotalNetPayAttribute(float $value) : int
    {
        return $this->attributes['total_net_pay'] = $value * 100;
    }

    public function getTotalNetPayAttribute(?int $value = 0) : float
    {
        return $value / 100;
    }

    public function scopeAutopayGenerated($query)
    {
        return $query->whereHas('auditSubMdaSchedules', function ($query) {
            $query->whereNotNull('autopay_generated');
        });
    }

    public function uploadComplete()
    {
        return $this->auditSubMdaSchedules()->where('uploaded', 0)->doesntExist();
    }

    public function autopayGenerationComplete()
    {
        return $this->auditSubMdaSchedules()->whereNull('autopay_generated')->doesntExist();
    }

    public function autopayUploadComplete()
    {
        return $this->auditSubMdaSchedules()->whereNull('autopay_uploaded')->doesntExist();
    }

    public function analysisIsComplete()
    {
        return $this->auditSubMdaSchedules()->whereNull('analysed')->doesntExist();
    }

    public function auditSubMdaScheduleWasUpdated()
    {
        $this->total_net_pay = $this->auditSubMdaSchedules()->sum('total_net_pay') / 100;
        $this->head_count = $this->auditSubMdaSchedules()->sum('head_count');
        $this->uploaded = $this->uploadComplete();

        $this->save();

        $this->auditPayrollCategory->auditMdaScheduleWasUpdated();
    }

    public function auditAutopayWasUploaded()
    {
        $this->autopay_uploaded = $this->autopayUploadComplete();

        $this->save();
    }

    public function analysisWasCompleted()
    {
        $this->analysed = $this->analysisIsComplete();

        $this->save();

        $this->auditPayrollCategory->analysisStatusWasUpdated();
    }

    public function auditAutopayWasGenerated()
    {
        $this->autopay_generated = $this->autopayGenerationComplete();

        $this->save();

        $this->auditPayrollCategory->autopayStatusWasUpdated();
    }

    public function autopayTotalAmount()
    {
        return $this->auditSubMdaSchedules()
                    ->first()
                    ->autopayTotalAmount();
    }

    public function autopayItemCount()
    {
        return $this->auditSubMdaSchedules()->first()->autopayItemCount();
    }

    public function payScheduleWasCleared()
    {
        $this->analysed = false;
        $this->autopay_generated = false;
        $this->autopay_uploaded = false;

        $this->save();
        $this->auditSubMdaScheduleWasUpdated();
    }
}
