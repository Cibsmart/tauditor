<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Bank;
use App\Models\Domain;
use App\Models\PaymentType;
use App\Models\BeneficiaryType;
use App\Models\PaymentCredential;
use Faker\Generator as Faker;

$factory->define(PaymentCredential::class, function (Faker $faker) {
    return [
        'corporate_code' => $faker->randomElement(['TPA', 'TLA']),
        'payment_type_id' => factory(PaymentType::class),
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
