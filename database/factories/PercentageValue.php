<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\PercentageValue;
use Faker\Generator as Faker;

$factory->define(PercentageValue::class, function (Faker $faker) {
    return [
        'percentage' => $faker->numberBetween(1, 20),
    ];
});
