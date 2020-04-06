<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class FixedValue extends Model
{
    protected $guarded = [];

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
