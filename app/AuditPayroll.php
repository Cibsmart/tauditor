<?php

namespace App;

use Illuminate\Support\Str;
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

    public function auditMdaSchedules()
    {
        return $this->hasManyThrough(AuditMdaSchedule::class, AuditPayrollCategory::class);
    }

    public function createdBy()
    {
        return $this->user->name;
    }

    public function dateCreated()
    {
        return $this->created_at->timezone('Africa/Lagos')->diffForHumans();
    }

    public function setTotalNetPayAttribute(float $value) : int
    {
        return $this->attributes['total_net_pay'] = $value * 100;
    }

    public function getTotalNetPayAttribute(?int $value = 0) : float
    {
        return $value / 100;
    }

    public function month($abbreviation = false)
    {
        if (! $abbreviation) {
            return "$this->month_name $this->year";
        }

        $month = Str::of($this->month_name)->limit(3, '');
        $year = Str::of($this->year)->substr(2, 2);

        return "$month $year";
    }
}
