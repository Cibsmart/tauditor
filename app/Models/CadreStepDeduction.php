<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CadreStepDeduction extends Model
{
    protected $guarded = [];

    public function deduction() : BelongsTo
    {
        return $this->belongsTo(Deduction::class);
    }

    public function cadreStep() : BelongsTo
    {
        return $this->belongsTo(CadreStep::class);
    }
}
