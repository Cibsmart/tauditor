<?php


namespace App\Contracts;

use App\Beneficiary;

interface Computable
{
    public function compute(Beneficiary $beneficiary);
}
