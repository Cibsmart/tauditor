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
use function array_merge;

/**
 * @property int id
 *
 * @property mixed valuable
 */
class Allowance extends Model
{
    protected $guarded = [];


    /*
    |-------------------------------------------------------------------------------
    | Relationship
    |-------------------------------------------------------------------------------
    */
    /**
     * A Polymorphic relationship to Fixed or Percentage value types
     * @return MorphTo
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

    /**
     * Get the Allowance Amount
     * @param  float|null  $base_value
     * @return float
     */
    public function amount(float $base_value = null) : float
    {
        return $this->valuable->amount($base_value);
    }


    /**
     * Apply Selected Allowance to a Beneficiary
     * @param  Beneficiary  $beneficiary
     * @param  int|null  $allowable_id
     * @return Allowance
     */
    public function applyTo(Beneficiary $beneficiary, int $allowable_id = null) : Allowance
    {
        $attributes = [
            'amount' => $this->amount($beneficiary->basic()),
            'beneficiary_id' => $beneficiary->id,
        ];

        $attributes = $allowable_id
            ? array_merge($attributes, ['allowable_id' => $allowable_id])
            : $attributes;

        $this->allowanceDetails()->create($attributes);

        return $this;
    }
}
