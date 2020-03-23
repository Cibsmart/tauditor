<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BeneficiaryType extends Model
{
    protected $guarded = [];

    public function designations()
    {
        return $this->hasMany(Designation::class);
    }

    public function mdas()
    {
        return $this->hasMany(Mda::class);
    }
}
