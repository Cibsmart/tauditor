<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @property mixed gradeLevel
 */
class Cadre extends Model
{
    protected $guarded = [];

    public function structure() : BelongsTo
    {
        return $this->belongsTo(Structure::class);
    }

    public function gradeLevel() : BelongsTo
    {
        return $this->belongsTo(GradeLevel::class);
    }

    public function steps() : HasMany
    {
        return $this->hasMany(CadreStep::class);
    }

    public function allowables() : MorphMany
    {
        return $this->morphMany(Allowable::class, 'allowable');
    }
}
