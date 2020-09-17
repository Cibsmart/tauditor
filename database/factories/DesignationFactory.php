<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\BeneficiaryType;
use App\Models\Designation;
use Faker\Generator as Faker;

$factory->define(Designation::class, function (Faker $faker) {
    return [
        'name' => $faker->country,
        'beneficiary_type_id' => factory(BeneficiaryType::class),
    ];
});
