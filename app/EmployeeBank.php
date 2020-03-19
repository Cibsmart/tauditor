<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeBank extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected $with = ['bankable'];

    //A polymorphic relationship to either Bank or Microfinance
    public function bankable()
    {
        return $this->morphTo();
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
