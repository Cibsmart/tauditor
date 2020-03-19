<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\EmployeeBank;
use Faker\Generator as Faker;

$factory->define(EmployeeBank::class, function (Faker $faker) {
    return [
        'account_number' => $faker->bankAccountNumber,
        'employee_id' => $faker->randomNumber(),
    ];
});
