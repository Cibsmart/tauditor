<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MicrofinanceBankSchedule extends Model
{
    use SoftDeletes;

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
