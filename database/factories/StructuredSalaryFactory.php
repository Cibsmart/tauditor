<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\CadreStep;
use App\Models\SalaryStructure;
use App\Models\StructuredSalary;
use Faker\Generator as Faker;

$factory->define(StructuredSalary::class, function (Faker $faker) {
    return [
        'cadre_step_id' => factory(CadreStep::class),
    ];
});
