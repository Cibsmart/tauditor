<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Mda;
use App\SubMda;
use App\MdaDetail;
use App\SubSubMda;
use App\Beneficiary;
use Faker\Generator as Faker;

$factory->define(MdaDetail::class, function (Faker $faker) {
    return [
        'beneficiary_id' => factory(Beneficiary::class),
        'mda_id' => factory(Mda::class),
    ];
});
