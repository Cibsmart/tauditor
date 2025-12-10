<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meta extends Model
{
    public function metable()
    {
        return $this->morphTo();
    }

    public function scopeIsActive($query, $name)
    {
        return $query->where('name', $name)
                     ->where('value', '>', 0)
                     ->whereNotNull('value');
    }
}
