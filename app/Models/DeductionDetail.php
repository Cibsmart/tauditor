<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property mixed deduction
 * @property mixed amount
 * @property mixed id
 */
class DeductionDetail extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    /*
    |-------------------------------------------------------------------------------
    | Relationship
    |-------------------------------------------------------------------------------
    */
    public function beneficiary() : BelongsTo
    {
        return $this->belongsTo(Beneficiary::class);
    }

    public function deduction() : BelongsTo
    {
        return $this->belongsTo(Deduction::class);
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
