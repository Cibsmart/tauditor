<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AllowanceName extends Model
{
    protected $guarded = [];

    public function allowances() : HasMany
    {
        return $this->hasMany(Allowance::class);
    }
}
