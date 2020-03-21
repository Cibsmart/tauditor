<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Carbon\Carbon;
use App\WorkDetail;
use App\Beneficiary;
use App\Designation;
use Faker\Generator as Faker;

$factory->define(WorkDetail::class, function (Faker $faker) {
    return [
        'beneficiary_id' => factory(Beneficiary::class),
        'designation_id' => factory(Designation::class),
        'date_of_appointment' => Carbon::now()->subYears($faker->randomElement([5, 6, 7, 8, 9, 10])),
        'place_of_appointment' => $faker->company,
        'confirmed' => $faker->randomElement([Carbon::now()->subYears($faker->randomElement([1, 2, 3])), null]),
        'last_promotion_date' => $faker->randomElement([Carbon::now()->subYears($faker->randomElement([1, 2, 3])), null]),
        'retirement_date' => $faker->randomElement([Carbon::now()->subYears($faker->randomElement([1, 2, 3])), null]),
    ];
});
