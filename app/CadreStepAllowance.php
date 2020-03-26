<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CadreStepAllowance extends Model
{
    protected $guarded = [];

    protected $with = ['allowance'];

    public function allowance() : BelongsTo
    {
        return $this->belongsTo(Allowance::class);
    }

    public function cadreStep() : BelongsTo
    {
        return $this->belongsTo(CadreStep::class);
    }
}
