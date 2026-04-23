<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AuditOtherPaySchedule extends Model
{
    use HasFactory;

    protected $guarded = [];

    // A polymorphic relationship to either Bank or Microfinance
    public function bankable(): MorphTo
    {
        return $this->morphTo();
    }

    public function otherPayrollCategory()
    {
        return $this->belongsTo(OtherAuditPayrollCategory::class);
    }

    public function setAmountAttribute(float $value): int
    {
        return $this->attributes['amount'] = $value * 100;
    }

    public function getAmountAttribute(?int $value = 0): float
    {
        return $value / 100;
    }
}
