<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\SalaryDetail;
use App\PersonalizedSalary;
use Faker\Generator as Faker;

$factory->define(PersonalizedSalary::class, function (Faker $faker) {
    return [
        'salary_detail_id' => factory(SalaryDetail::class),
    ];
});
