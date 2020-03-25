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
    public function valuable()
    {
        return $this->morphTo();
    }

    public function allowanceDetails()
    {
        return $this->hasMany(AllowanceDetail::class);
    }

    public function allowanceName()
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
        return $this->valuable->amount($value);
    }

    public function applyTo(Beneficiary $beneficiary)
    {
        $this->allowanceDetails()->create([
            'amount' => $this->amount(500),
            'beneficiary_id' => $beneficiary->id
        ]);

        return $this;
    }
}
