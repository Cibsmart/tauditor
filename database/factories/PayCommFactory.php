<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Domain;
use App\Models\PayComm;
use Faker\Generator as Faker;


$factory->define(PayComm::class, function (Faker $faker) {
    return [
        'code'           => $faker->countryCode,
        'name'           => $faker->country,
        'account_number' => $faker->bankAccountNumber,
        'commission'     => $faker->numberBetween(50, 200),
        'bankable_type'  => $faker->randomElement(['commercial', 'micro_finance']),
        'bankable_id'    => $faker->numberBetween(1, 15),
        'domain_id'      => factory(Domain::class),
    ];
});
