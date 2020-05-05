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
        'analysed'          => 'boolean',
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

    public function auditPaymentCategories()
    {
        return $this->hasMany(AuditPayrollCategory::class);
    }

    public function createdBy()
    {
        return $this->user->name;
    }

    public function dateCreated()
    {
        return $this->created_at->timezone('Africa/Lagos')->diffForHumans();
    }

    public function month()
    {
        return "$this->month_name $this->year";
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
