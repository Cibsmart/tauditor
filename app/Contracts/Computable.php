<?php


namespace App\Contracts;

use App\Models\Beneficiary;

interface Computable
{
    public function compute(Beneficiary $beneficiary);
}
