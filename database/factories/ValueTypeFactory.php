<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\ValueType;
use Faker\Generator as Faker;

$factory->define(ValueType::class, function (Faker $faker) {
    return [
        'id' => $faker->unique()->countryCode,
        'name' => $faker->country,
    ];
});
