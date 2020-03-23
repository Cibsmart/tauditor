<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SalaryDetail extends Model
{
    protected $guarded = [];

    //A polymorphic relationship to either Structure or Personalize Salary
    public function payable()
    {
        return $this->morphTo();
    }

    public function beneficiary()
    {
        return $this->belongsTo(Beneficiary::class);
    }
}
