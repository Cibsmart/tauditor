<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\CadreStep;
use App\Deduction;
use App\CadreStepDeduction;
use Faker\Generator as Faker;

$factory->define(CadreStepDeduction::class, function (Faker $faker) {
    return [
        'deduction_id' => factory(Deduction::class),
        'cadre_step_id' => factory(CadreStep::class),
    ];
});
