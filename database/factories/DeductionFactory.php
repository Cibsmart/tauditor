<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Domain;
use App\Deduction;
use App\DeductionName;
use Faker\Generator as Faker;

$factory->define(Deduction::class, function (Faker $faker) {
    return [
        'deduction_name_id' => factory(DeductionName::class),
        'domain_id' => factory(Domain::class),
    ];
});
