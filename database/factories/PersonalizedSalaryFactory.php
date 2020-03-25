<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\SalaryDetail;
use App\PersonalizedSalary;
use Faker\Generator as Faker;

$factory->define(PersonalizedSalary::class, function (Faker $faker) {
    return [
        'monthly_basic' => $faker->numberBetween(10000, 100000),
    ];
});
