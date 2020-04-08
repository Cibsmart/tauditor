<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Beneficiary;
use App\BeneficiaryStatus;
use Faker\Generator as Faker;

$factory->define(BeneficiaryStatus::class, function (Faker $faker) {
    return [
        'active' => $faker->randomElement([0, 1]),
        'beneficiary_id' => factory(Beneficiary::class),
    ];
});
