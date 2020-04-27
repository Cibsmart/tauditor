<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int id
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
