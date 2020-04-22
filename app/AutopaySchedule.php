<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AutopaySchedule extends Model
{
    protected $guarded = [];

    public function setAmountAttribute(float $value) : int
    {
        return $this->attributes['amount'] = $value * 100;
    }

    public function getAmountAttribute(int $value) : float
    {
        return $value / 100;
    }
}
