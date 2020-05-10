<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MaritalStatus extends Model
{
    public $incrementing = false;

    protected $keyType = 'string';

    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
