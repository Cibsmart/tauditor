<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Deduction;
use App\Models\DeductionDetail;
use Faker\Generator as Faker;


$factory->define(DeductionDetail::class, function (Faker $faker) {

    $deduction = factory(Deduction::class)->create();

    return [
        'beneficiary_id' => factory(Beneficiary::class),
        'deduction_id' => $deduction->id,
        'amount' => $deduction->amount(),
    ];
});
