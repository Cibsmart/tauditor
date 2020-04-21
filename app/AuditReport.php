<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AuditReport extends Model
{
    protected $guarded = [];

    protected $casts = [
        'current_values' => 'array',
        'previous_values' => 'array',
    ];

    public function reportable()
    {
        return $this->morphTo();
    }
}
