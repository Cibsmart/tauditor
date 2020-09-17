<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Bank;
use App\Models\Domain;
use App\Models\MicroFinanceBank;
use Faker\Generator as Faker;

$factory->define(MicroFinanceBank::class, function (Faker $faker) {
    return [
        'name' => $faker->company,
        'account_number' => $faker->bankAccountNumber,
        'bank_id' => factory(Bank::class),
        'domain_id' => factory(Domain::class),
    ];
});
