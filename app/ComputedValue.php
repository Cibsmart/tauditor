<?php

namespace App;

use App\Compute\Tax;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use function resolve;

/**
 * @property mixed computer
 */
class ComputedValue extends Model
{

    public function allowance()
    {
        return $this->morphTo(Allowance::class, 'valuable');
    }

    public function deduction()
    {
        return $this->morphTo(Deduction::class, 'valuable');
    }


    public function amount(Beneficiary $beneficiary = null)
    {
        if(! $beneficiary){
            return Str::upper($this->computer);
        }

        return resolve($this->computer)->compute($beneficiary);
    }

    public function type()
    {
        return 'COMPUTED';
    }
}
