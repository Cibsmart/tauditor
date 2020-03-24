<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PersonalizedSalary extends Model
{
    protected $guarded = [];

    public function salary()
    {
        return $this->morphOne(SalaryDetail::class, 'payable');
    }

    public function basic()
    {
        return $this->morphOne(BasicPay::class, 'basicable');
    }



    public function basic_pay()
    {
        return $this->basic;
    }


}
