<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CadreStep extends Model
{
    protected $guarded = [];

    public function cadre()
    {
        return $this->belongsTo(Cadre::class);
    }

    public function setMonthlyBasicAttribute($value)
    {
        return $this->attributes['monthly_basic'] = $value * 100;
    }

    public function getMonthlyBasicAttribute($value)
    {
        return $value / 100;
    }
}
