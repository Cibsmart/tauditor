<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use phpDocumentor\Reflection\Types\Float_;
use phpDocumentor\Reflection\Types\Integer;
use phpDocumentor\Reflection\Types\String_;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Allowance extends Model
{
    protected $guarded = [];


    /*
    |-------------------------------------------------------------------------------
    | Relationship
    |-------------------------------------------------------------------------------
    */
    public function valuable() : MorphTo
    {
        return $this->morphTo();
    }

    public function allowanceDetails() : HasMany
    {
        return $this->hasMany(AllowanceDetail::class);
    }

    public function allowanceName() : BelongsTo
    {
        return $this->belongsTo(AllowanceName::class);
    }

    public function cadreAllowance() : HasOne
    {
        return $this->hasOne(CadreStepAllowance::class);
    }


    /*
    |-------------------------------------------------------------------------------
    | Methods
    |-------------------------------------------------------------------------------
    */
    public function amount(float $base_value = null) : float
    {
        return $this->valuable->amount($base_value);
    }

    public function applyTo(Beneficiary $beneficiary) : Allowance
    {
        $this->allowanceDetails()->create([
            'amount' => $this->amount($beneficiary->basic()),
            'beneficiary_id' => $beneficiary->id
        ]);

        return $this;
    }
}
