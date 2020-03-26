<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use phpDocumentor\Reflection\Types\Float_;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class PersonalizedSalary extends Model
{
    protected $guarded = [];

    public function salary() : MorphOne
    {
        return $this->morphOne(SalaryDetail::class, 'payable');
    }

    public function basicPay() : float
    {
        return $this->monthly_basic;
    }

    public function setMonthlyBasicAttribute(float $value) : int
    {
        return $this->attributes['monthly_basic'] = $value * 100;
    }

    public function getMonthlyBasicAttribute(int $value) : float
    {
        return $value / 100;
    }
}
