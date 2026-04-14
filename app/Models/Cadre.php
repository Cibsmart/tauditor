<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

}
