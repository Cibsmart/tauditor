<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\CadreStep;
use App\Models\Deduction;
use App\Models\CadreStepDeduction;
use Faker\Generator as Faker;

$factory->define(CadreStepDeduction::class, function (Faker $faker) {
    return [
        'deduction_id' => factory(Deduction::class),
        'cadre_step_id' => factory(CadreStep::class),
    ];
});
