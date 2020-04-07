<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int id
 * @property mixed name
 */
class DeductionName extends Model
{
    protected $guarded = [];

    public function deductions() : HasMany
    {
        return $this->hasMany(Deduction::class);
    }

    public function deductionType() : BelongsTo
    {
        return $this->belongsTo(DeductionType::class);
    }
}
