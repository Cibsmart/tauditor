<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\SubMda;
use App\Models\SubSubMda;
use Faker\Generator as Faker;

$factory->define(SubSubMda::class, function (Faker $faker) {
    return [
        'name' => $faker->country,
        'sub_mda_id' => factory(SubMda::class),
    ];
});
