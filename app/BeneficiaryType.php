<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BeneficiaryType extends Model
{
    protected $guarded = [];

    public function designations() : HasMany
    {
        return $this->hasMany(Designation::class);
    }

    public function mdas() : HasMany
    {
        return $this->hasMany(Mda::class);
    }
}
