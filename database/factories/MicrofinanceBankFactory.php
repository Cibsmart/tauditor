<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Bank;
use App\Domain;
use App\MicroFinanceBank;
use Faker\Generator as Faker;

$factory->define(MicroFinanceBank::class, function (Faker $faker) {
    return [
        'name' => $faker->company,
        'account_number' => $faker->bankAccountNumber,
        'bank_id' => factory(Bank::class),
        'domain_id' => factory(Domain::class),
    ];
});
