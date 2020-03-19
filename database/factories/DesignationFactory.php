<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\StaffType;
use App\Designation;
use Faker\Generator as Faker;

$factory->define(Designation::class, function (Faker $faker) {
    return [
        'code' => $faker->countryCode,
        'name' => $faker->country,
        'staff_type_id' => factory(StaffType::class),
    ];
});
