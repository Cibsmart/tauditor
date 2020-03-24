<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PercentageValue extends Model
{
    protected $guarded = [];

    public function value()
    {
        return $this->morphOne(Allowance::class, 'amountable');
    }

    public function amount($amount)
    {
        return $amount * $this->percentage / 100;
    }
}
