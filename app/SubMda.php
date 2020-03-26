<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubMda extends Model
{
    protected $guarded = [];

    protected $casts = [
        'has_sub' => 'boolean',
    ];

    public function subs() : HasMany
    {
        return $this->hasMany(SubSubMda::class);
    }
}
