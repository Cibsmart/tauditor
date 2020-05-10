<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Gender;
use Faker\Generator as Faker;

$factory->define(Gender::class, function (Faker $faker) {
    $gender = $faker->randomElement(['M', 'F']);
    return [
        'id' => $gender,
        'name' => fn () => $gender == 'M' ? 'Male' : 'Female',
    ];
});
