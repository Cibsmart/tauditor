<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int id
 * @property mixed deductionName
 * @property mixed valuable
 */

class Deduction extends Model
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

    public function deductionDetails() : HasMany
    {
        return $this->hasMany(DeductionDetail::class);
    }

    public function deductionName() : BelongsTo
    {
        return $this->belongsTo(DeductionName::class);
    }


    /*
    |-------------------------------------------------------------------------------
    | Methods
    |-------------------------------------------------------------------------------
    */
    public function amount(Beneficiary $beneficiary = null)
    {
        return $this->valuable->amount($beneficiary);
    }

    public function name()
    {
        return $this->deductionName->name;
    }

    public function deductionType()
    {
        return $this->deductionName->deductionType;
    }

    public function valueType()
    {
        return $this->valuable->type();
    }

    public function applyTo(Beneficiary $beneficiary)
    {
        $this->deductionDetails()->create([
            'amount' => $this->amount($beneficiary->basic()),
            'beneficiary_id' => $beneficiary->id
        ]);

        return $this;
    }
}
