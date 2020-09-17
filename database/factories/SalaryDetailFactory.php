<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Beneficiary;
use App\Models\SalaryDetail;
use Faker\Generator as Faker;

$factory->define(SalaryDetail::class, function (Faker $faker) {
    return [
        'beneficiary_id' => factory(Beneficiary::class),
    ];
});
