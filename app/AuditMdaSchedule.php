<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AuditMdaSchedule extends Model
{
    use SoftDeletes;

    protected $perPage = 30;

    protected $guarded = [];

    protected $casts = [
        'uploaded' => 'boolean',
    ];

    public function mda()
    {
        return $this->belongsTo(Mda::class);
    }

    public function auditPayroll()
    {
        return $this->belongsTo(AuditPayroll::class);
    }
}
