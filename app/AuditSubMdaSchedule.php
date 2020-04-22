<?php

namespace App;

use App\Traits\CanBeReported;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;

class AuditSubMdaSchedule extends Model
{
    use CanBeReported;

    protected $guarded = [];

    protected $casts = [
        'uploaded' => 'boolean',

    ];

    protected $dates = [
        'analysed',
        'autopay_generated',
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

    public function microfinanceSchedules()
    {
        return $this->hasMany(MicrofinanceBankSchedule::class);
    }

    public function totalNetPay()
    {
        return $this->auditPaySchedules()->sum('net_pay') / 100;
    }

    public function headCount()
    {
        return $this->auditPaySchedules()->count();
    }

    public function month()
    {
        return $this->auditMdaSchedule->auditPayroll->month_name;
    }

    public function year()
    {
        return $this->auditMdaSchedule->auditPayroll->year;
    }

    public function domain()
    {
        return $this->auditMdaSchedule->auditPayroll->domain;
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
        return $query->whereHas('microfinanceSchedules' );
    }

    public function analysisCompleted()
    {
        $this->analysed = Carbon::now();
        $this->save();
    }

    public function autopayGenerated()
    {
        $this->autopay_generated = Carbon::now();
        $this->save();
    }
}
