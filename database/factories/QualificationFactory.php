<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Qualification;
use App\Models\QualificationType;
use Faker\Generator as Faker;

$factory->define(Qualification::class, function (Faker $faker) {
    return [
        'qualification_type_id' => factory(QualificationType::class),
        'institution' => $faker->company,
        'grade' => $faker->sentence(3,false),
        'year' => $faker->year,
    ];
});
