<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\FixedValue;
use Faker\Generator as Faker;

$factory->define(FixedValue::class, function (Faker $faker) {
    return [
        'amount' => $faker->randomFloat(2, 100, 5000),
    ];
});
