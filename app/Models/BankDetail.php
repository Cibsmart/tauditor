<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class BankDetail extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    protected $with = ['bankable'];

    // A polymorphic relationship to either Bank or Microfinance
    public function bankable(): MorphTo
    {
        return $this->morphTo();
    }

    public function beneficiary(): BelongsTo
    {
        return $this->belongsTo(Beneficiary::class);
    }

    public function getBvnAttribute()
    {
        return $this->bank_verification_number;
    }
}
