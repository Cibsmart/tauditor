<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditPayroll extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'analysed' => 'boolean',
        'autopay_generated' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function domain() : BelongsTo
    {
        return $this->belongsTo(Domain::class);
    }

    public function auditMdaSchedules()
    {
        return $this->hasMany(AuditMdaSchedule::class);
    }

    public function auditReports()
    {
        return $this->hasMany(AuditReport::class);
    }

    public function createdBy()
    {
        return $this->user->name;
    }

    public function dateCreated()
    {
        return $this->created_at->timezone('Africa/Lagos')->diffForHumans();
    }

    public function noAutopaySchedule()
    {
        return $this->auditMdaSchedules()->whereHas('auditSubMdaSchedules', function($query){
            return $query->whereNotNull('autopay_generated');
        })->doesntExist();
    }
}
