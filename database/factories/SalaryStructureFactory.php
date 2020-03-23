<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Step;
use App\Structure;
use App\GradeLevel;
use App\SalaryStructure;
use Faker\Generator as Faker;

$factory->define(SalaryStructure::class, function (Faker $faker) {
    return [
        'structure_id' => factory(Structure::class),
        'grade_level_id' => factory(GradeLevel::class),
        'step_id' => factory(Step::class),
    ];
});
