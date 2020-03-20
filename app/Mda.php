<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Mda extends Model
{
    protected $guarded = [];

    protected $casts = [
        'has_sub' => 'boolean',
    ];
}
