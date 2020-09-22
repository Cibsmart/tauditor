<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\BankType;
use Faker\Generator as Faker;

$factory->define(BankType::class, function (Faker $faker) {
    return [
        'id'   => $faker->countryCode,
        'name' => $faker->country,
    ];
});
