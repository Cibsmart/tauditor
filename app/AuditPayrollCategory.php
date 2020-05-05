<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

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

    public function monthYear()
    {
        return $this->auditPayroll->month();
    }


    public function domain()
    {
        return $this->auditPayroll->domain;
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
}
