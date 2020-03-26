<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DeductionName extends Model
{
    protected $guarded = [];

    public function deductions() : HasMany
    {
        return $this->hasMany(Deduction::class);
    }
}
