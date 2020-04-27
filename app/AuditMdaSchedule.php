<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AuditMdaSchedule extends Model
{
    use SoftDeletes;

    protected $perPage = 35;

    protected $guarded = [];

    protected $casts = [
        'uploaded' => 'boolean',
        'has_sub'  => 'boolean',
        'pension'  => 'boolean',
    ];

    public function mda()
    {
        return $this->belongsTo(Mda::class);
    }

    public function auditPayroll()
    {
        return $this->belongsTo(AuditPayroll::class);
    }

    public function auditSubMdaSchedules()
    {
        return $this->hasMany(AuditSubMdaSchedule::class);
    }

    public function setTotalNetPayAttribute(float $value) : int
    {
        return $this->attributes['total_net_pay'] = $value * 100;
    }

    public function getTotalNetPayAttribute(?int $value = 0) : float
    {
        return $value / 100;
    }

    public function uploadeComplete()
    {
        return $this->auditSubMdaSchedules()->where('uploaded', 0)->doesntExist();
    }

    public function auditSubMdaScheduleWasUpdated()
    {
        $this->total_net_pay = $this->auditSubMdaSchedules()->sum('total_net_pay') / 100;
        $this->head_count = $this->auditSubMdaSchedules()->sum('head_count');
        $this->uploaded = $this->uploadeComplete();

        $this->save();
    }
}
