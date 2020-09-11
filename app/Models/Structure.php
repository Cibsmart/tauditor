<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Structure extends Model
{
    protected $guarded = [];

    public function cadre() : HasMany
    {
        return $this->hasMany(Cadre::class,);
    }

    public function steps() : HasManyThrough
    {
        return $this->hasManyThrough(
            CadreStep::class,
            Cadre::class,
            'structure_id',
            'step_id');
    }

    public function salaryDetails() : HasManyThrough
    {
        return $this->hasManyThrough(
            SalaryDetail::class,
            StructuredSalary::class,
            'salary_detail_id',
            'payable_id');
    }

    public function allowables() : MorphMany
    {
        return $this->morphMany(Allowable::class, 'allowable');
    }

    /**
     * Synchronize all SalaryStructure Allowances to a Beneficiary
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
