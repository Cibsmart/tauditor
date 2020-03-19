<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\BankDetail;
use Faker\Generator as Faker;

$factory->define(BankDetail::class, function (Faker $faker) {
    return [
        'account_number' => $faker->bankAccountNumber,
        'beneficiary_id' => $faker->randomNumber(),
    ];
});
