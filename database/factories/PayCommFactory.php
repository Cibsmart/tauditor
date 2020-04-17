<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Bank;
use App\Domain;
use App\PayComm;
use Faker\Generator as Faker;


$factory->define(PayComm::class, function (Faker $faker) {
    $bank = factory(Bank::class)->create();
    return [
        'code' => $faker->countryCode,
        'name' => $faker->country,
        'account_number' => $faker->bankAccountNumber,
        'commission' => $faker->numberBetween(50,200),
        'bankable_type' => $bank->bankableType(),
        'bankable_id' => $bank->id,
        'domain_id' => factory(Domain::class),
    ];
});
