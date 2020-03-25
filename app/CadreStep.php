<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CadreStep extends Model
{
    protected $guarded = [];

    /*
    |-------------------------------------------------------------------------------
    | Relationships
    |-------------------------------------------------------------------------------
    */
    public function cadre()
    {
        return $this->belongsTo(Cadre::class);
    }

    public function allowances()
    {
        return $this->hasMany(CadreStepAllowance::class);
    }

    public function deductions()
    {
        return $this->hasMany(CadreStepDeduction::class);
    }


    /*
    |-------------------------------------------------------------------------------
    | Methods
    |-------------------------------------------------------------------------------
    */
    public function setMonthlyBasicAttribute($value)
    {
        return $this->attributes['monthly_basic'] = $value * 100;
    }

    public function getMonthlyBasicAttribute($value)
    {
        return $value / 100;
    }
}
