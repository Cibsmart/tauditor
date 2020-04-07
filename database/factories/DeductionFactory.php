<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Domain;
use App\Deduction;
use App\FixedValue;
use App\DeductionName;
use Faker\Generator as Faker;

$factory->define(Deduction::class, function (Faker $faker) {

    $valuable = factory(FixedValue::class)->create();

    return [
        'deduction_name_id' => factory(DeductionName::class),
        'domain_id' => factory(Domain::class),
        'valuable_type' => Str::snake(class_basename($valuable)),
        'valuable_id' => $valuable->id,
    ];
});
