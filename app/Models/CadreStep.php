<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @property mixed allowables
 */
class CadreStep extends Model
{
    protected $guarded = [];

    /*
    |-------------------------------------------------------------------------------
    | Relationships
    |-------------------------------------------------------------------------------
    */
    public function cadre() : BelongsTo
    {
        return $this->belongsTo(Cadre::class);
    }

    public function step() : BelongsTo
    {
        return $this->belongsTo(Step::class);
    }

    /**
     * @return MorphMany
     */
    public function allowables() : MorphMany
    {
        return $this->morphMany(Allowable::class, 'allowable')->with('allowance');
    }

    /*
    |-------------------------------------------------------------------------------
    | Methods
    |-------------------------------------------------------------------------------
    */
    public function setMonthlyBasicAttribute(float $value) : int
    {
        return $this->attributes['monthly_basic'] = round($value * 100);
    }

    public function getMonthlyBasicAttribute(int $value) : float
    {
        return $value / 100;
    }

    /**
     * Synchronize all Domain Allowances to a Beneficiary
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
