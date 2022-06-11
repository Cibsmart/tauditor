<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Cadre;
use App\Models\CadreStep;
use App\Models\Step;
use Faker\Generator as Faker;

$factory->define(CadreStep::class, function (Faker $faker) {
    return [
        'cadre_id' => factory(Cadre::class),
        'step_id' => factory(Step::class),
        'monthly_basic' => $faker->numberBetween(10000, 1000000),
    ];
});
