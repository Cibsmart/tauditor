<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BankDetail extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected $with = ['bankable'];

    //A polymorphic relationship to either Bank or Microfinance
    public function bankable() : MorphTo
    {
        return $this->morphTo();
    }

    public function beneficiary() : BelongsTo
    {
        return $this->belongsTo(Beneficiary::class);
    }
}
