<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FixedValue extends Model
{
    protected $guarded = [];

    public function allowance()
    {
        return $this->morphOne(Allowance::class, 'valuable');
    }

    public function deduction()
    {
        return $this->morphOne(Deduction::class, 'valuable');
    }

    public function amount() : float
    {
        return $this->amount;
    }

    public function type()
    {
        return 'FIXED';
    }

    public function setAmountAttribute(float $value) : int
    {
        return $this->attributes['Amount'] = $value * 100;
    }

    public function getAmountAttribute(int $value) : float
    {
        return $value / 100;
    }
}
