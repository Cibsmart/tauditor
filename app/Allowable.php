<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int id
 */
class Allowable extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    public function allowable() : MorphTo
    {
        return $this->morphTo();
    }

    public function allowance() : BelongsTo
    {
        return $this->belongsTo(Allowance::class);
    }
}
