<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Domain;
use App\Models\Allowance;
use App\Models\FixedValue;
use Faker\Generator as Faker;
use App\Models\AllowanceName;

$factory->define(Allowance::class, function (Faker $faker){

    $valuable = factory(FixedValue::class)->create();

    return [
        'allowance_name_id' => factory(AllowanceName::class),
        'domain_id'         => factory(Domain::class),
        'valuable_type'     => Str::snake(class_basename($valuable)),
        'valuable_id'       => $valuable->id,
    ];
});
