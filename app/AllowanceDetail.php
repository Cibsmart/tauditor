<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AllowanceDetail extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    public function beneficiary()
    {
        return $this->belongsTo(Beneficiary::class);
    }

    public function allowance()
    {
        return $this->belongsTo(Allowance::class);
    }

    public function unapply()
    {
        return AllowanceDetail::where('id', $this->id)->delete();
    }
}
