<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    public function allowances()
    {
        return $this->cadreStep->allowances->allowance;
    }
}
