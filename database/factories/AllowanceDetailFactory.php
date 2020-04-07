<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Allowance;
use App\Beneficiary;
use App\AllowanceDetail;
use Faker\Generator as Faker;

$factory->define(AllowanceDetail::class, function (Faker $faker) {

    $allowance = factory(Allowance::class)->create();

    return [
        'beneficiary_id' => factory(Beneficiary::class),
        'allowance_id' => $allowance->id,
        'amount' => $allowance->amount(),
    ];
});
