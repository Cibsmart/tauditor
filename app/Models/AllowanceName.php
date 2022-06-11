<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AllowanceName extends Model
{
    protected $guarded = [];

    public function allowances() : HasMany
    {
        return $this->hasMany(Allowance::class);
    }

    public function allowanceType() : BelongsTo
    {
        return $this->belongsTo(AllowanceType::class);
    }

    public function typeName()
    {
        return $this->allowanceType()->type;
    }
}
