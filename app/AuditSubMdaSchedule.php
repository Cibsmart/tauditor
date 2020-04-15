<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AuditSubMdaSchedule extends Model
{
    protected $guarded = [];

    protected $casts = [
        'uploaded' => 'boolean',
    ];

    public function auditMdaSchedule()
    {
        return $this->belongsTo(AuditMdaSchedule::class);
    }

    public function auditPaySchedules()
    {
        return $this->hasMany(AuditPaySchedule::class);
    }

    public function totalNetPay()
    {
        return $this->auditPaySchedules()->sum('net_pay') / 100;
    }

    public function headCount()
    {
        return $this->auditPaySchedules()->count();
    }

    public function setTotalNetPayAttribute(float $value) : int
    {
        return $this->attributes['total_net_pay'] = $value * 100;
    }

    public function getTotalNetPayAttribute(?int $value = 0) : float
    {
        return $value / 100;
    }
}
