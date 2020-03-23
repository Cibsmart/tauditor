<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BasicPay extends Model
{
    protected $guarded = [];

    //A polymorphic relationship to either Personalized Salary or Salary Structure
    public function basicable()
    {
        return $this->morphTo();
    }

    public function setAnnualAttribute($value)
    {
        return $this->attributes['annual'] = $value * 100;
    }

    public function getAnnualAttribute($value)
    {
        return $value / 100;
    }

    public function setMonthlyAttribute($value)
    {
        return $this->attributes['monthly'] = $value * 100;
    }

    public function getMonthlyAttribute($value)
    {
        return $value / 100;
    }
}
