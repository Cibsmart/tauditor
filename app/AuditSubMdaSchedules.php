<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AuditSubMdaSchedules extends Model
{
    protected $guarded = [];

    protected $casts = [
        'uploaded' => 'boolean',
    ];

    public function auditMdaSchedule()
    {
        return $this->belongsTo(AuditMdaSchedule::class);
    }
}
