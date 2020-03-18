<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\MaritalStatus;
use Faker\Generator as Faker;

$factory->define(MaritalStatus::class, function (Faker $faker) {
    $status = $faker->randomElement(['S', 'M']);

    return [
        'code' => $status,
        'name' => fn () => $status == 'S' ? 'Single' : 'Married',
    ];
});
