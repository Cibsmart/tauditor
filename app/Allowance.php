<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Allowance extends Model
{
    protected $guarded = [];


    /*
    |-------------------------------------------------------------------------------
    | Relationship
    |-------------------------------------------------------------------------------
    */
    public function amountable()
    {
        return $this->morphTo();
    }

    public function allowance_details()
    {
        return $this->hasMany(AllowanceDetail::class);
    }

    public function allowance_name()
    {
        return $this->belongsTo(AllowanceName::class);
    }


    /*
    |-------------------------------------------------------------------------------
    | Methods
    |-------------------------------------------------------------------------------
    */
    public function amount($value)
    {
        return $this->amountable->amount($value);
    }

    public function applyTo(Beneficiary $beneficiary)
    {
        $this->allowance_details()->create([
            'amount' => $this->amount(500),
            'beneficiary_id' => $beneficiary->id
        ]);

        return $this;
    }
}
