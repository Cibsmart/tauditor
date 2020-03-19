<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MicroFinanceBank extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    public function beneficiaries()
    {
        return $this->morphMany(BankDetail::class, 'bankable');
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }
}
