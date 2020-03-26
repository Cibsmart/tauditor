<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class PercentageValue extends Model
{
    protected $guarded = [];

    public function value() : MorphOne
    {
        return $this->morphOne(Allowance::class, 'valuable');
    }

    public function amount(float $base_value) : float
    {
        return $base_value * $this->percentage / 100;
    }
}
