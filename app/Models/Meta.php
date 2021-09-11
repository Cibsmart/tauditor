<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Meta extends Model
{
    use HasFactory;

    public function metable()
    {
        return $this->morphTo();
    }

    public function scopeisActive($query, $name)
    {
        return $query->where('name', $name)
                     ->where('value', '>', 0)
                     ->whereNotNull('value');
    }
}
