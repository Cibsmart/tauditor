<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MicrofinanceBankSchedule extends Model
{
    protected $guarded = [];

    public function microFinanceBank()
    {
        return $this->belongsTo(MicroFinanceBank::class);
    }

    public function setAmountAttribute(float $value) : int
    {
        return $this->attributes['amount'] = $value * 100;
    }

    public function getAmountAttribute(int $value) : float
    {
        return $value / 100;
    }
}
