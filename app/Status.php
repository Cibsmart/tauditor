<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int id
 */
class Status extends Model
{
    protected $guarded = [];

    protected $casts = [
        'state' => 'boolean',
    ];
}
