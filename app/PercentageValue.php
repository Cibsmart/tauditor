<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * @property mixed percentage
 */
class PercentageValue extends Model
{
    protected $guarded = [];

    public function value() : MorphOne
    {
        return $this->morphOne(Allowance::class, 'valuable');
    }

    public function amount(float $base_value = null) : float
    {
        return $base_value
            ? $base_value * $this->percentage / 100
            : $this->percentage;
    }

    public function type()
    {
        return 'PERCENTAGE';
    }
}
