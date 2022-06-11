<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Beneficiary;
use App\Models\BeneficiaryType;
use App\Models\Domain;
use App\Models\Gender;
use App\Models\LocalGovernment;
use App\Models\MaritalStatus;
use App\Models\State;
use Carbon\Carbon;
use Faker\Generator as Faker;

$factory->define(Beneficiary::class, function (Faker $faker) {
    return [
        'last_name'           => $faker->lastName,
        'first_name'          => $faker->firstName,
        'middle_name'         => $faker->firstName,
        'date_of_birth'       => Carbon::now()->subYears($faker->numberBetween(20, 60)),
        'gender_id'           => factory(Gender::class),
        'marital_status_id'   => factory(MaritalStatus::class),
        'state_id'            => factory(State::class),
        'local_government_id' => factory(LocalGovernment::class),
        'phone_number'        => $faker->phoneNumber,
        'email'               => $faker->email,
        'address_line_1'      => $faker->streetAddress,
        'address_line_2'      => $faker->secondaryAddress,
        'address_city'        => $faker->city,
        'address_state'       => $faker->state,
        'address_country'     => $faker->country,
        'pensioner'           => 0,
        'domain_id'           => factory(Domain::class),
        'beneficiary_type_id' => factory(BeneficiaryType::class),
    ];
});

$factory->state(Beneficiary::class, 'pensioner', ['pensioner' => 1]);
