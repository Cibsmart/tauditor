<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cadre extends Model
{
    protected $guarded = [];

    public function structure() : BelongsTo
    {
        return $this->belongsTo(Structure::class);
    }

    public function steps() : HasMany
    {
        return $this->hasMany(CadreStep::class);
    }
}
