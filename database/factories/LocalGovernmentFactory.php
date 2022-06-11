<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\LocalGovernment;
use Faker\Generator as Faker;

$factory->define(LocalGovernment::class, function (Faker $faker) {
    return [
        'state_id' => 1,
        'name' => 'Aba',
    ];
});
