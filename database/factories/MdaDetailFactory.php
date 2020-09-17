<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Mda;
use App\Models\MdaDetail;
use App\Models\Beneficiary;
use Faker\Generator as Faker;

$factory->define(MdaDetail::class, function (Faker $faker){
    return [
        'beneficiary_id' => factory(Beneficiary::class),
        'mda_id'         => factory(Mda::class),
    ];
});
