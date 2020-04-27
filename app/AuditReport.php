<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AuditReport extends Model
{
    protected $guarded = [];

    protected $casts = [
        'current_value'  => 'array',
        'previous_value' => 'array',
    ];

    public function reportable()
    {
        return $this->morphTo();
    }
}
