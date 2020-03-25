<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Deduction extends Model
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

    public function deduction_details()
    {
        return $this->hasMany(DeductionDetail::class);
    }

    public function deduction_name()
    {
        return $this->belongsTo(DeductionName::class);
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
        $this->deduction_details()->create([
            'amount' => $this->amount(500),
            'beneficiary_id' => $beneficiary->id
        ]);

        return $this;
    }
}
