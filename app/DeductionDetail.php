<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeductionDetail extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    public function beneficiary() : BelongsTo
    {
        return $this->belongsTo(Beneficiary::class);
    }

    public function deduction() : BelongsTo
    {
        return $this->belongsTo(Deduction::class);
    }

    public function unapply() : int
    {
        return $this->delete();
    }
}
