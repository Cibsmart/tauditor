<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditOtherPaySchedule extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function otherPayrollCategory()
    {
        return $this->belongsTo(OtherAuditPayrollCategory::class);
    }

    public function setAmountAttribute(float $value) : int
    {
        return $this->attributes['amount'] = $value * 100;
    }

    public function getAmountAttribute(?int $value = 0) : float
    {
        return $value / 100;
    }
}
