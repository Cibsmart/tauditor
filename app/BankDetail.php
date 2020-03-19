<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BankDetail extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected $with = ['bankable'];

    //A polymorphic relationship to either Bank or Microfinance
    public function bankable()
    {
        return $this->morphTo();
    }

    public function beneficiary()
    {
        return $this->belongsTo(Beneficiary::class);
    }
}
