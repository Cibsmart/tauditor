<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    protected $guarded = [];

    public function beneficiaries()
    {
        return $this->morphMany(BankDetail::class, 'bankable');
    }
}
