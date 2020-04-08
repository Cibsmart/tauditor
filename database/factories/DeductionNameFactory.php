<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Domain;
use App\DeductionName;
use App\DeductionType;
use Faker\Generator as Faker;

$factory->define(DeductionName::class, function (Faker $faker) {
    return [
        'deduction_type_id' => factory(DeductionType::class),
        'code' => $faker->countryCode,
        'name' => $faker->country,
        'domain_id' => factory(Domain::class),
    ];
});
