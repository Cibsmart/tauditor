<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Cadre;
use App\Structure;
use App\GradeLevel;
use Faker\Generator as Faker;

$factory->define(Cadre::class, function (Faker $faker) {
    return [
        'structure_id' => factory(Structure::class),
        'grade_level_id' => factory(GradeLevel::class),
    ];
});
