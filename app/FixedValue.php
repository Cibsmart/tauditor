<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class FixedValue extends Model
{
    protected $guarded = [];

    public function value() : MorphOne
    {
        return $this->morphOne(Allowance::class, 'valuable');
    }

    public function amount() : float
    {
        return $this->amount;
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
