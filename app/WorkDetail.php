<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkDetail extends Model
{
    protected $guarded = [];

    protected $casts = [];

    public function designation() : BelongsTo
    {
        return $this->belongsTo(Designation::class);
    }
}
