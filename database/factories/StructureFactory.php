<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Domain;
use App\Models\Structure;
use Faker\Generator as Faker;

$factory->define(Structure::class, function (Faker $faker) {
    return [
        'code' => $faker->countryCode,
        'name' => $faker->country,
        'domain_id' => factory(Domain::class),
    ];
});
