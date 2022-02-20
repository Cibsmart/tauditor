<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
