<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\AssignableType;
use Faker\Generator as Faker;

$factory->define(AssignableType::class, function (Faker $faker) {
    return [
        'id' => $faker->unique()->countryCode,
        'name' => $faker->country,
    ];
});
