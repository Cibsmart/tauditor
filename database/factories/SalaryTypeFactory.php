<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\SalaryType;
use Faker\Generator as Faker;

$factory->define(SalaryType::class, function (Faker $faker) {
    return [
        'id' => $faker->unique()->countryCode,
        'name' => $faker->country,
    ];
});
