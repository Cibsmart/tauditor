<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Mda extends Model
{
    protected $guarded = [];

    protected $casts = [
        'has_sub' => 'boolean',
    ];

    public function subs()
    {
        return $this->hasMany(SubMda::class);
    }
}
