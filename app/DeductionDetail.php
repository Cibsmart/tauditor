<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DeductionDetail extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    public function beneficiary()
    {
        return $this->belongsTo(Beneficiary::class);
    }

    public function deduction()
    {
        return $this->belongsTo(Deduction::class);
    }

    public function unapply()
    {
        return $this->delete();
    }
}
