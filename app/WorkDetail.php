<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WorkDetail extends Model
{
    protected $guarded = [];

    protected $casts = [];

    public function designation()
    {
        return $this->belongsTo(Designation::class);
    }
}
