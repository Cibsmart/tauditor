<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Structure extends Model
{
    protected $guarded = [];

    public function cadre()
    {
        return $this->hasMany(Cadre::class, );
    }

    public function steps()
    {
        return $this->hasManyThrough(
            CadreStep::class,
            Cadre::class,
            'grade_level_id',
            'structure_grade_level_id');
    }

    public function salaryDetails()
    {
        return $this->hasManyThrough(
            SalaryDetail::class,
            StructuredSalary::class,
            'salary_detail_id',
            'payable_id');
    }
}
