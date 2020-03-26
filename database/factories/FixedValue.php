<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\FixedValue;
use Faker\Generator as Faker;

$factory->define(FixedValue::class, function (Faker $faker) {
    return [
        'amount' => $faker->numberBetween(10000, 1000000),
    ];
});
