<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Step;
use App\CadreStep;
use App\GradeLevel;
use App\SalaryDetail;
use App\SalaryStructure;
use App\StructuredSalary;
use Faker\Generator as Faker;

$factory->define(StructuredSalary::class, function (Faker $faker) {
    return [
        'cadre_step_id' => factory(CadreStep::class),
    ];
});
