<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SubMda extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $casts = [
        'has_sub' => 'boolean',
    ];

    public function subs() : HasMany
    {
        return $this->hasMany(SubSubMda::class);
    }
}
