<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Qualification;
use Faker\Generator as Faker;

$factory->define(Qualification::class, function (Faker $faker) {
    return [
        'qualification_type_id' => factory(\App\QualificationType::class),
        'institution' => $faker->company,
        'grade' => $faker->sentence(3,false),
        'year' => $faker->year,
    ];
});
