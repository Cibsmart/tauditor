<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class MdaStructure extends Model
{
    protected $guarded = [];

    public function allowables() : MorphMany
    {
        return $this->morphMany(Allowable::class, 'allowable');
    }
}
