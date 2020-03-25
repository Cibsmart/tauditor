<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\AllowanceName;
use App\AllowanceType;
use Faker\Generator as Faker;

$factory->define(AllowanceName::class, function (Faker $faker) {
    return [
        'allowance_type_id' => factory(AllowanceType::class),
        'code' => $faker->countryCode,
        'name' => $faker->country,
    ];
});
