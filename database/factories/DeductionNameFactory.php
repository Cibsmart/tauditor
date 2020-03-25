<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\DeductionName;
use App\DeductionType;
use Faker\Generator as Faker;

$factory->define(DeductionName::class, function (Faker $faker) {
    return [
        'deduction_type_id' => factory(DeductionType::class),
        'code' => $faker->countryCode,
        'name' => $faker->country,
    ];
});
