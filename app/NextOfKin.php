<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NextOfKin extends Model
{
    protected $guarded = [];

    protected $casts = [
        'address' => AddressCast::class,
    ];
}
