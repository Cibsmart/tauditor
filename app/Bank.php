<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Bank extends Model
{
    protected $guarded = [];

    /*
    |-------------------------------------------------------------------------------
    | Relationship
    |-------------------------------------------------------------------------------
    */
    public function beneficiaries() : MorphMany
    {
        return $this->morphMany(BankDetail::class, 'bankable');
    }

    public function bankableType()
    {
        return 'commercial';
    }

    public function bankCode()
    {
        return $this->code;
    }
}
