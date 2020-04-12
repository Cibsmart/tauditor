<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\ValueType;
use Faker\Generator as Faker;

$factory->define(ValueType::class, function (Faker $faker) {
    return [
        'code' => $faker->countryCode,
        'name' => $faker->country,
    ];
});
