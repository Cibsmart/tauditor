<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\QualificationType;
use Faker\Generator as Faker;

$factory->define(QualificationType::class, function (Faker $faker) {
    return [
        'id' => $faker->countryCode,
        'name' => $faker->domainName,
    ];
});
