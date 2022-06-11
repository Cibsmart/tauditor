<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SalaryDetail extends Model
{
    protected $guarded = [];

    //A polymorphic relationship to either Structure or Personalize Salary
    public function payable() : MorphTo
    {
        return $this->morphTo();
    }

    public function beneficiary() : BelongsTo
    {
        return $this->belongsTo(Beneficiary::class);
    }

    public function basicPay() : float
    {
        return $this->payable->basicPay();
    }
}
