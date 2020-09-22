<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use function array_merge;

/**
 * @property int id
 *
 * @property mixed valuable
 * @property mixed allowanceName
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
     * @param  Beneficiary|null  $beneficiary
     * @return float
     */
    public function amount(Beneficiary $beneficiary = null) : float
    {
        return $this->valuable->amount($beneficiary);
    }

    public function name()
    {
        return $this->allowanceName->name;
    }

    public function allowanceType()
    {
        return $this->allowanceName->allowanceType;
    }

    public function valueType()
    {
        return $this->valuable->type();
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
            'amount'         => $this->amount($beneficiary->basic()),
            'beneficiary_id' => $beneficiary->id,
        ];

        $attributes = $allowable_id
            ? array_merge($attributes, ['allowable_id' => $allowable_id])
            : $attributes;

        $this->allowanceDetails()->create($attributes);

        return $this;
    }

    public function scopeFilters($query, array $filters) : void
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', '%'.$search.'%')
                      ->orWhere('amount', 'like', '%'.$search.'%')
                      ->orWhere('type', 'like', '%'.$search.'%')
                      ->orWhere('value_type', 'like', '%'.$search.'%');
            });
        });
    }
}
