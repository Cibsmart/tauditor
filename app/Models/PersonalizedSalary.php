<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PersonalizedSalary extends Model
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
        return $this->monthly_basic;
    }

    public function cadre() : Cadre
    {
        return new Cadre();
    }

    public function gradeLevel() : GradeLevel
    {
        return new GradeLevel();
    }

    public function step() : Step
    {
        return new Step();
    }

    public function setMonthlyBasicAttribute(float $value) : int
    {
        return $this->attributes['monthly_basic'] = round($value * 100);
    }

    public function getMonthlyBasicAttribute(int $value) : float
    {
        return $value / 100;
    }
}
