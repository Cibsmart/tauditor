<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StaffType extends Model
{
    protected $guarded = [];

    public function designations()
    {
        return $this->hasMany(Designation::class);
    }
}
