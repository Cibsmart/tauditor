<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AuditReport extends Model
{
    use SoftDeletes;

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
