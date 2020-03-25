<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CadreStepDeduction extends Model
{
    protected $guarded = [];

    public function deduction()
    {
        return $this->belongsTo(Deduction::class);
    }

    public function cadreStep()
    {
        return $this->belongsTo(CadreStep::class);
    }
}
