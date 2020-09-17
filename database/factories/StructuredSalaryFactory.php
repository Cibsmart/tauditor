<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\CadreStep;
use Faker\Generator as Faker;
use App\Models\SalaryStructure;
use App\Models\StructuredSalary;

$factory->define(StructuredSalary::class, function (Faker $faker){
    return [
        'cadre_step_id' => factory(CadreStep::class),
    ];
});
