<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FidelityLoanSchedule extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'confirmation_sent' => 'datetime'
    ];

    public function deductions()
    {
        return $this->hasMany(FidelityLoanDeduction::class);
    }

    public function setAmountAttribute($value)
    {
        return $this->attributes['amount'] = $value * 100;
    }

    public function getAmountAttribute($value)
    {
        return $value / 100;
    }

    public function totalAmount()
    {
        return $this->deductions()->sum('amount') / 100;
    }
}
