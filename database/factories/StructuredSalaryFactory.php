<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Step;
use App\GradeLevel;
use App\SalaryDetail;
use App\SalaryStructure;
use App\StructuredSalary;
use Faker\Generator as Faker;

$factory->define(StructuredSalary::class, function (Faker $faker) {
    return [
        'salary_detail_id' => factory(SalaryDetail::class),
        'salary_structure_id' => factory(SalaryStructure::class),
        'grade_level_id' => factory(GradeLevel::class),
        'step_id' => factory(Step::class),
    ];
});
