<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property mixed cadreStep
 */
class StructuredSalary extends Model
{
    protected $guarded = [];

    /*
    |-------------------------------------------------------------------------------
    | Relationships
    |-------------------------------------------------------------------------------
    */
    public function salary() : MorphOne
    {
        return $this->morphOne(SalaryDetail::class, 'payable');
    }

    public function cadreStep() : BelongsTo
    {
        return $this->belongsTo(CadreStep::class);
    }


    /*
    |-------------------------------------------------------------------------------
    | Methods
    |-------------------------------------------------------------------------------
    */
    public function basicPay() : float
    {
        return $this->cadreStep->monthly_basic;
    }

    public function cadre() : Cadre
    {
        return $this->cadreStep->cadre;
    }

    public function gradeLevel() : GradeLevel
    {
        return $this->cadreStep->cadre->gradeLevel;
    }

    public function step() : Step
    {
        return $this->cadreStep->step;
    }

    public function allowances() : Collection
    {
        return $this->cadreStep->allowances;
    }

    public function deductions() : Collection
    {
        return $this->cadreStep->deductions;
    }

    /**
     * Synchronize all Allowances of a Salary Structure to a Beneficiary
     * @param  Beneficiary  $beneficiary
     * @return StructuredSalary
     */
    public function syncAllowances(Beneficiary $beneficiary) : StructuredSalary
    {
        $allowances = $this->allowances();

        foreach ($allowances as $allowance) {
            $allowance->allowance->applyTo($beneficiary);
        }

        return $this;
    }

    /**
     * Synchronize all Deductions of a Salary Structure to a Beneficiary
     * @param  Beneficiary  $beneficiary
     * @return StructuredSalary
     */
    public function syncDeductions(Beneficiary $beneficiary) : StructuredSalary
    {
        $deductions = $this->deductions();

        foreach ($deductions as $deduction) {
            $deduction->deduction->applyTo($beneficiary);
        }

        return $this;
    }
}
