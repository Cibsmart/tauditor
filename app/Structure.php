<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Structure extends Model
{
    protected $guarded = [];

    public function salary_structures()
    {
        return $this->hasMany(SalaryStructure::class);
    }

    public function grades()
    {
        return $this->hasMany(StructureGradeLevel::class, );
    }

    public function steps()
    {
        return $this->hasManyThrough(
            StructureGradeLevelStep::class,
            StructureGradeLevel::class,
            'grade_level_id',
            'structure_grade_level_id');
    }

    public function salary_details()
    {
        return $this->hasManyThrough(
            SalaryDetail::class,
            StructuredSalary::class,
            'salary_detail_id',
            'payable_id');
    }
}
