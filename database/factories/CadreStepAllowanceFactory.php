<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\CadreStep;
use App\Models\Allowance;
use App\Models\CadreStepAllowance;
use Faker\Generator as Faker;

$factory->define(CadreStepAllowance::class, function (Faker $faker) {
    return [
        'allowance_id' => factory(Allowance::class),
        'cadre_step_id' => factory(CadreStep::class),
    ];
});
