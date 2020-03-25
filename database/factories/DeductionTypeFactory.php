<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Domain;
use App\DeductionType;
use Faker\Generator as Faker;

$factory->define(DeductionType::class, function (Faker $faker) {
    return [
        'code' => $faker->countryCode,
        'name' => $faker->country,
        'domain_id' => factory(Domain::class),
    ];
});
