<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property mixed allowance
 * @property mixed amount
 * @property mixed id
 */
class AllowanceDetail extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    /*
    |-------------------------------------------------------------------------------
    | Relationships
    |-------------------------------------------------------------------------------
    */
    public function beneficiary() : BelongsTo
    {
        return $this->belongsTo(Beneficiary::class);
    }

    public function allowance() : BelongsTo
    {
        return $this->belongsTo(Allowance::class);
    }


    /*
    |-------------------------------------------------------------------------------
    | Methods
    |-------------------------------------------------------------------------------
    */
    public function unapply() : int
    {
        return $this->delete();
    }

    public function setAmountAttribute(float $value) : int
    {
        return $this->attributes['Amount'] = round($value * 100);
    }

    public function getAmountAttribute(int $value) : float
    {
        return $value / 100;
    }
}
