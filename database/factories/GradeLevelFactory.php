<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\GradeLevel;
use Faker\Generator as Faker;

$factory->define(GradeLevel::class, function (Faker $faker) {
    $number = $faker->numberBetween(1, 17);
    return [
        'code' => $number,
        'name' => 'GL ' . $number,
    ];
});
