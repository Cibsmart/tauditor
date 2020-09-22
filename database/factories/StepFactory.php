<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Step;
use Faker\Generator as Faker;

$factory->define(Step::class, function (Faker $faker) {
    $number = $faker->numberBetween(1, 15);
    return [
        'code' => $number,
        'name' => 'Step ' . $number,
    ];
});
