<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    public function allowances() : HasMany
    {
        return $this->hasMany(CadreStepAllowance::class);
    }

    public function deductions() : HasMany
    {
        return $this->hasMany(CadreStepDeduction::class);
    }


    /*
    |-------------------------------------------------------------------------------
    | Methods
    |-------------------------------------------------------------------------------
    */
    public function setMonthlyBasicAttribute(float $value) : int
    {
        return $this->attributes['monthly_basic'] = $value * 100;
    }

    public function getMonthlyBasicAttribute(int $value) : float
    {
        return $value / 100;
    }
}
