<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\QualificationType;
use Faker\Generator as Faker;

$factory->define(QualificationType::class, function (Faker $faker) {
    return [
        'id' => $faker->countryCode,
        'name' => $faker->domainName,
    ];
});
