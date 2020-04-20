<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AuditReport extends Model
{
    protected $guarded = [];

    public function reportable()
    {
        return $this->morphTo();
    }
}
