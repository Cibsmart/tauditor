<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GradeLevel extends Model
{
    protected $guarded = [];

    public function steps()
    {
        return $this->hasMany(Step::class);
    }
}
