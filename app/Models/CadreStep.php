<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
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

    public function step() : BelongsTo
    {
        return $this->belongsTo(Step::class);
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

}
