<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AuditReport extends Model
{
    use HasFactory;
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
