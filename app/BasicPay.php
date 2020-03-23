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
}
