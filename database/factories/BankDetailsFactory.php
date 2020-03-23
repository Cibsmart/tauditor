<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\BankDetail;
use App\Beneficiary;
use Faker\Generator as Faker;

$factory->define(BankDetail::class, function (Faker $faker) {
    return [
        'account_number' => $faker->bankAccountNumber,
        'beneficiary_id' => factory(Beneficiary::class),
    ];
});
