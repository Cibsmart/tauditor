<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Step;
use App\Cadre;
use App\CadreStep;
use Faker\Generator as Faker;

$factory->define(CadreStep::class, function (Faker $faker) {
    return [
        'cadre_id' => factory(Cadre::class),
        'step_id' => factory(Step::class),
        'monthly_basic' => $faker->numberBetween(10000, 1000000),
    ];
});
