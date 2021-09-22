<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FidelityLoanDeduction extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    public function mandate()
    {
        return $this->belongsTo(LoanMandate::class);
    }

    public function schedule()
    {
        return $this->belongsTo(AuditSubMdaSchedule::class);
    }

    public function setAmountAttribute($value)
    {
        return $this->attributes['amount'] = $value * 100;
    }

    public function getAmountAttribute($value)
    {
        return $value * 100;
    }

    public function getFormattedAmountAttribute()
    {
        return number_format($this->amount, 2);
    }
}
