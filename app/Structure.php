<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Structure extends Model
{
    protected $guarded = [];

    public function cadre() : HasMany
    {
        return $this->hasMany(Cadre::class, );
    }

    public function steps() : HasManyThrough
    {
        return $this->hasManyThrough(
            CadreStep::class,
            Cadre::class,
            'structure_id',
            'step_id');
    }

    public function salaryDetails() : HasManyThrough
    {
        return $this->hasManyThrough(
            SalaryDetail::class,
            StructuredSalary::class,
            'salary_detail_id',
            'payable_id');
    }
}
