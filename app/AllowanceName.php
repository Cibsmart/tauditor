<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AllowanceName extends Model
{
    protected $guarded = [];

    public function allowances()
    {
        return $this->hasMany(Allowance::class);
    }
}
