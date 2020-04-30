<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Bank;
use App\Domain;
use App\PaymentType;
use App\BeneficiaryType;
use App\PaymentCredential;
use Faker\Generator as Faker;

$factory->define(PaymentCredential::class, function (Faker $faker) {
    return [
        'payment_type' => factory(PaymentType::class),
        'terminal_id' => $faker->randomNumber(8, true),
        'account_number' => $faker->bankAccountNumber,
        'account_name' => $faker->company,
        'pan' => $faker->bankAccountNumber,
        'account_type' => $faker->randomElement(['00', '10', '20']),
        'bank_id' => factory(Bank::class),
        'beneficiary_type_id' => factory(BeneficiaryType::class),
        'domain_id' => factory(Domain::class),
    ];
});
