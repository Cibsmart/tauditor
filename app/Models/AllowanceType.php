<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AllowanceType extends Model
{
    protected $guarded = [];

    public function allowanceName() : HasMany
    {
        return $this->hasMany(AllowanceName::class);
    }
}
