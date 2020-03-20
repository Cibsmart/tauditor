<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Relationship;
use Faker\Generator as Faker;

$factory->define(Relationship::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
    ];
});
