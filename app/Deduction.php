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

    public function deductionDetails()
    {
        return $this->hasMany(DeductionDetail::class);
    }

    public function deductionName()
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
        $this->deductionDetails()->create([
            'amount' => $this->amount(500),
            'beneficiary_id' => $beneficiary->id
        ]);

        return $this;
    }
}
