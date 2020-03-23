<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StructuredSalary extends Model
{
    protected $guarded = [];

    public function salary()
    {
        return $this->morphOne(SalaryDetail::class, 'payable');
    }

    public function structure()
    {
        return $this->belongsTo(SalaryStructure::class);
    }

    public function basic_amount()
    {
        return $this->structure()->basic->annual;
    }
}
