<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FixedValue extends Model
{
    protected $guarded = [];

    public function value()
    {
        return $this->morphOne(Allowance::class, 'amountable');
    }

    public function amount($allowance)
    {
        return $this->amount;
    }

    public function setAmountAttribute($value)
    {
        return $this->attributes['monthly'] = $value * 100;
    }

    public function getAmountAttribute($value)
    {
        return $value / 100;
    }
}
