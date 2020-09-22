<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @method static where(string $string, $bank_name)
 */
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
