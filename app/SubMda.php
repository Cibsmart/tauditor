<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubMda extends Model
{
    protected $guarded = [];

    protected $casts = [
        'has_sub' => 'boolean',
    ];

    public function subs()
    {
        return $this->hasMany(SubSubMda::class);
    }
}
