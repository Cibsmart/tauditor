<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Allowance;
use App\AllowanceName;
use Faker\Generator as Faker;

$factory->define(Allowance::class, function (Faker $faker) {
    return [
        'allowance_name_id' => factory(AllowanceName::class),
    ];
});
