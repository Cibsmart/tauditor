<?php

namespace App\Models;

use App\Traits\CanBeReported;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @method static find($audit_sub_mda)
 */
class AuditSubMdaSchedule extends Model
{
    use HasFactory;
    use CanBeReported;

    protected $guarded = [];

    protected $casts = [
        'uploaded' => 'boolean',
        'analysed' => 'datetime',
        'autopay_generated'  => 'datetime',
        'autopay_uploaded'  => 'datetime',
    ];

    public function auditMdaSchedule()
    {
        return $this->belongsTo(AuditMdaSchedule::class);
    }

    public function auditPaySchedules()
    {
        return $this->hasMany(AuditPaySchedule::class);
    }

    public function autopaySchedules()
    {
        return $this->hasMany(AutopaySchedule::class);
    }

    public function auditReports()
    {
        return $this->hasMany(AuditReport::class);
    }

    public function microfinanceSchedules()
    {
        return $this->hasMany(MicrofinanceBankSchedule::class);
    }

    public function fidelitySchedules()
    {
        return $this->hasOne(FidelityLoanSchedule::class);
    }

    public function fidelityDeductions()
    {
        return $this->hasMany(FidelityLoanDeduction::class);
    }

    public function totalNetPay()
    {
        return $this->auditPaySchedules()->sum('net_pay') / 100;
    }

    public function headCount()
    {
        return $this->auditPaySchedules()->count();
    }

    public function fidelityLoanAmount()
    {
        return $this->fidelityDeductions()->sum('amount') / 100;
    }

    public function month()
    {
        return $this->auditMdaSchedule->auditPayrollCategory->auditPayroll->month_name;
    }

    public function year()
    {
        return $this->auditMdaSchedule->auditPayrollCategory->auditPayroll->year;
    }

    public function domain()
    {
        return $this->auditMdaSchedule->auditPayrollCategory->auditPayroll->domain;
    }

    public function payrollCategory()
    {
        return $this->auditMdaSchedule->auditPayrollCategory;
    }

    public function mdaBeneficiaryCodes()
    {
        return $this->autopaySchedules->pluck('beneficiary_code')->join('');
    }

    public function mdaBeneficiaryAccountNumbers()
    {
        return $this->autopaySchedules->pluck('account_number')->join('');
    }

    public function mdaTotalAmount()
    {
        return $this->autopaySchedules()->sum('amount');
    }

    public function setTotalNetPayAttribute(float $value) : int
    {
        return $this->attributes['total_net_pay'] = $value * 100;
    }

    public function getTotalNetPayAttribute(?int $value = 0) : float
    {
        return $value / 100;
    }

    public function scopeUploaded($query)
    {
        return $query->where('uploaded', 1);
    }

    public function scopeAutopayNotGenerated($query)
    {
        return $query->where('autopay_generated', null);
    }

    public function scopeAutopayGenerated($query)
    {
        return $query->whereNotNull('autopay_generated');
    }

    public function scopeNotAnalysed($query)
    {
        return $query->where('analysed', null);
    }

    public function scopeAnalysed($query)
    {
        return $query->whereNotNull('analysed');
    }

    public function scopeHasMicrofinance($query)
    {
        return $query->whereHas('microfinanceSchedules');
    }

    public function scopeMfbSchedules($query)
    {
        return $query->join('microfinance_bank_schedules', 'audit_sub_mda_schedules.id', '=', 'microfinance_bank_schedules.audit_sub_mda_schedule_id');
    }

    public function analysisCompleted()
    {
        $this->analysed = Carbon::now();
        $this->save();

        $this->auditMdaSchedule->analysisWasCompleted();
    }

    public function autopayGenerated()
    {
        $this->autopay_generated = Carbon::now();
        $this->save();

        $this->auditMdaSchedule->auditAutopayWasGenerated();
    }

    public function autopayUploaded()
    {
        $this->autopay_uploaded = Carbon::now();
        $this->save();

        $this->auditMdaSchedule->auditAutopayWasUploaded();
    }

    public function autopayTotalAmount()
    {
        return $this->autopaySchedules()->sum('amount');
    }

    public function autopayItemCount()
    {
        return $this->autopaySchedules()->count();
    }

    public function payScheduleWasCleared()
    {
        $this->uploaded = false;
        $this->analysed = null;
        $this->autopay_generated = null;
        $this->autopay_uploaded = null;
        $this->total_net_pay = 0;
        $this->head_count = 0;
        $this->user_id = null;
        $this->file_path = null;

        $this->save();

        $this->auditMdaSchedule->payScheduleWasCleared();
    }
}
