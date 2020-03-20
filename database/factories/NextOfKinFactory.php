<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\NextOfKin;
use Faker\Generator as Faker;

$factory->define(NextOfKin::class, function (Faker $faker) {
    return [
        'last_name' => $faker->lastName,
        'first_name' => $faker->firstName,
        'middle_name' => $faker->firstName,
        'relationship_id' => $faker->randomElement([1,2,3,4,5,6,7]),
        'phone_number' => $faker->phoneNumber,
        'email' => $faker->email,
        'address_line_1' => $faker->streetAddress,
        'address_line_2' => $faker->secondaryAddress,
        'address_city' => $faker->city,
        'address_state' => $faker->state,
        'address_country' => $faker->country,
    ];
});
