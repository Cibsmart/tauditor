<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Deduction;
use App\Models\DeductionName;
use App\Models\Domain;
use App\Models\FixedValue;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(Deduction::class, function (Faker $faker) {
    $valuable = factory(FixedValue::class)->create();

    return [
        'deduction_name_id' => factory(DeductionName::class),
        'domain_id' => factory(Domain::class),
        'valuable_type' => Str::snake(class_basename($valuable)),
        'valuable_id' => $valuable->id,
    ];
});
