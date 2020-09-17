<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Domain;
use App\Models\BeneficiaryType;
use Faker\Generator as Faker;

$factory->define(BeneficiaryType::class, function (Faker $faker) {
    return [
        'id' => $faker->countryCode,
        'name' => $faker->country,
        'domain_id' => factory(Domain::class),
    ];
});
