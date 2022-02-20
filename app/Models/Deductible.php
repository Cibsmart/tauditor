<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int id
 * @method static create(array $array)
 */
class Deductible extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    public function deductible() : MorphTo
    {
        return $this->morphTo();
    }

    public function deduction() : BelongsTo
    {
        return $this->belongsTo(Deduction::class);
    }
}
