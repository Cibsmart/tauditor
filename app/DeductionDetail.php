<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        return $this->attributes['Amount'] = $value * 100;
    }

    public function getAmountAttribute(int $value) : float
    {
        return $value / 100;
    }
}
