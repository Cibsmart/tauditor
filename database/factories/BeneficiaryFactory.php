<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\State;
use App\Gender;
use App\Beneficiary;
use Carbon\Carbon;
use App\MaritalStatus;
use App\LocalGovernment;
use Faker\Generator as Faker;

$factory->define(Beneficiary::class, function (Faker $faker) {
    return [
        'last_name' => $faker->lastName,
        'first_name' => $faker->firstName,
        'middle_name' => $faker->firstName,
        'date_of_birth' => Carbon::now()->subYears($faker->numberBetween(20, 60)),
        'gender_id' => factory(Gender::class),
        'marital_status_id' => factory(MaritalStatus::class),
        'state_id' => factory(State::class),
        'local_government_id' => factory(LocalGovernment::class),
        'residential_address' => $faker->address,
        'phone_number' => $faker->phoneNumber,
        'email' => $faker->email,
        'active' => $faker->randomElement([0, 1]),
    ];
});
