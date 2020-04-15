<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @property mixed code
 */
class BeneficiaryType extends Model
{
    protected $guarded = [];

    protected $casts = [
        'pensioners' => 'boolean'
    ];

    public function designations() : HasMany
    {
        return $this->hasMany(Designation::class);
    }

    public function mdas() : HasMany
    {
        return $this->hasMany(Mda::class);
    }

    public function allowables() : MorphMany
    {
        return $this->morphMany(Allowable::class, 'allowable');
    }

    /**
     * Synchronize all BeneficiaryType Allowances to a Beneficiary
     * @param  Beneficiary  $beneficiary
     * @return Beneficiary
     */
    public function syncAllowancesTo(Beneficiary $beneficiary) : Beneficiary
    {
        $allowables = $this->allowables;

        foreach ($allowables as $allowable) {
            $beneficiary->applyAllowance($allowable->allowance, $allowable->id);
        }

        return $beneficiary;
    }
}
