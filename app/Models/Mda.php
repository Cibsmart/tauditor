<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mda extends Model
{
    protected $guarded = [];

    protected $casts = [
        'has_sub' => 'boolean',
        'active' => 'boolean',
    ];

    public function subs() : HasMany
    {
        return $this->hasMany(SubMda::class);
    }

    public function scopeIsActive($query)
    {
        return $query->where('active', 1);
    }

    public function beneficiaryType()
    {
        return $this->belongsTo(BeneficiaryType::class);
    }
}
