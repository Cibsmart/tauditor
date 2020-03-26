<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DeductionType extends Model
{
    protected $guarded = [];

    public function deductionName() : HasMany
    {
        return $this->hasMany(DeductionName::class);
    }
}
