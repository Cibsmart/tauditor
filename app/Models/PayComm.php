<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PayComm extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    //A polymorphic relationship to either Bank or Microfinance
    public function bankable() : MorphTo
    {
        return $this->morphTo();
    }

    public function setCommissionAttribute(float $value) : int
    {
        return $this->attributes['commission'] = $value * 100;
    }

    public function getCommissionAttribute(int $value) : float
    {
        return $value / 100;
    }
}
