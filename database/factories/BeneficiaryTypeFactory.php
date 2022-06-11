<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\BeneficiaryType;
use App\Models\Domain;
use Faker\Generator as Faker;

$factory->define(BeneficiaryType::class, function (Faker $faker) {
    return [
        'id' => $faker->countryCode,
        'name' => $faker->country,
        'domain_id' => factory(Domain::class),
    ];
});
