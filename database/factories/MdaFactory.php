<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Mda;
use App\Models\BeneficiaryType;
use Faker\Generator as Faker;

$factory->define(Mda::class, function (Faker $faker) {
    return [
        'code' => $faker->countryCode,
        'name' => $faker->country,
        'beneficiary_type_id' => factory(BeneficiaryType::class),
    ];
});
