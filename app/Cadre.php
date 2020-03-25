<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cadre extends Model
{
    protected $guarded = [];

    public function structure()
    {
        return $this->belongsTo(Structure::class);
    }

    public function steps()
    {
        return $this->hasMany(CadreStep::class);
    }
}
