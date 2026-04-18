<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Gender extends Model
{
    use HasFactory;
    public $incrementing = false;

    protected $keyType = 'string';

    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
