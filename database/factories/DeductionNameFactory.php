<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\DeductionName;
use App\Models\DeductionType;
use App\Models\Domain;
use Faker\Generator as Faker;

$factory->define(DeductionName::class, function (Faker $faker) {
    return [
        'deduction_type_id' => factory(DeductionType::class),
        'code' => $faker->countryCode,
        'name' => $faker->country,
        'domain_id' => factory(Domain::class),
    ];
});
