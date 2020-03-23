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

    public function basic_pay()
    {
        return $this->structure()->basic;
    }

    public function grade_level()
    {
        return $this->belongsTo(GradeLevel::class);
    }

    public function step()
    {
        return $this->belongsTo(Step::class);
    }
}
