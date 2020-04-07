<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Domain;
use App\Allowance;
use App\FixedValue;
use App\AllowanceName;
use App\PercentageValue;
use Faker\Generator as Faker;

$factory->define(Allowance::class, function (Faker $faker) {

    $valuable = factory(FixedValue::class)->create();

    return [
        'allowance_name_id' => factory(AllowanceName::class),
        'domain_id' => factory(Domain::class),
        'valuable_type' => Str::snake(class_basename($valuable)),
        'valuable_id' => $valuable->id,
    ];
});
