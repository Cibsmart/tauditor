<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Beneficiary;
use App\Models\BeneficiaryStatus;
use Faker\Generator as Faker;

$factory->define(BeneficiaryStatus::class, function (Faker $faker) {
    return [
        'active' => $faker->randomElement([0, 1]),
    ];
});
