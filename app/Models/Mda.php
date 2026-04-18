<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Mda extends Model
{
    use HasFactory;
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
