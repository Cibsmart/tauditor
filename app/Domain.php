<?php

namespace App;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @property int id
 * @property mixed code
 * @property mixed name
 * @property mixed allowables
 */
class Domain extends Model
{
    protected $guarded = [];

    public function beneficiaries() : HasMany
    {
        return $this->hasMany(Beneficiary::class);
    }

    public function beneficiaryTypes() : HasMany
    {
        return $this->hasMany(BeneficiaryType::class);
    }

    public function structures() : HasMany
    {
        return $this->hasMany(Structure::class);
    }

    public function allowances() : HasMany
    {
        return $this->hasMany(Allowance::class);
    }

    public function deductions() : HasMany
    {
        return $this->hasMany(Deduction::class);
    }

    public function deductionTypes() : HasMany
    {
        return $this->hasMany(DeductionType::class);
    }

    public function deductionNames() : HasMany
    {
        return $this->hasMany(DeductionName::class);
    }

    public function allowables() : MorphMany
    {
        return $this->morphMany(Allowable::class, 'allowable');
    }

    public function payrolls()
    {
        return $this->hasMany(Payroll::class);
    }

    public function auditPayrolls()
    {
        return $this->hasMany(AuditPayroll::class);
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
