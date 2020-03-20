<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Mda;
use App\SubMda;
use Faker\Generator as Faker;

$factory->define(SubMda::class, function (Faker $faker) {
    return [
        'name' => $faker->country,
        'mda_id' => factory(Mda::class),
    ];
});
