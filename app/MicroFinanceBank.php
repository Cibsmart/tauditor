<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MicroFinanceBank extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    public function employees()
    {
        return $this->morphMany(EmployeeBank::class, 'bankable');
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }
}
