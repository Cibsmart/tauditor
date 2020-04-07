<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use function number_format;

/**
 * @property mixed percentage
 */
class PercentageValue extends Model
{
    protected $guarded = [];

    public function allowance()
    {
        return $this->morphTo(Allowance::class, 'valuable');
    }

    public function deduction()
    {
        return $this->morphTo(Deduction::class, 'valuable');
    }


    public function amount(Beneficiary $beneficiary = null) : float
    {
        if(! $beneficiary){
            return $this->percentage;
        }

        $amount = $beneficiary->basic() * $this->percentage / 100;
        return number_format($amount, 2, '.', '');
    }

    public function type()
    {
        return 'PERCENTAGE';
    }
}
