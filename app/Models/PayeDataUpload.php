<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayeDataUpload extends Model
{
    protected $guarded = [];

    protected $casts = [
       'successful' => 'boolean',
       'failed' => 'boolean',
       'client' => 'boolean',
       'server' => 'boolean',
    ];
}
