<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SalaryStructure extends Model
{
    protected $guarded = [];

    public function basic()
    {
        return $this->morphOne(BasicPay::class, 'basicable');
    }
}
