<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AssignableType extends Model
{
    public $incrementing = false;

    protected $keyType = 'string';
}
