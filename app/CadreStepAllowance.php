<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CadreStepAllowance extends Model
{
    protected $guarded = [];

    public function allowance()
    {
        return $this->belongsTo(Allowance::class);
    }

    public function cadreStep()
    {
        return $this->belongsTo(CadreStep::class);
    }
}
