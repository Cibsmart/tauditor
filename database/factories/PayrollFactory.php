<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Domain;
use App\Models\Payroll;
use App\Models\User;
use Faker\Generator as Faker;

$factory->define(Payroll::class, function (Faker $faker) {
    return [
        'month' => $faker->month('now'),
        'month_name' => $faker->monthName('now'),
        'year' => $faker->year('now'),
        'user_id' => factory(User::class),
        'domain_id' => factory(Domain::class),
    ];
});
