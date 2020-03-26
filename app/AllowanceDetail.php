<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
}
