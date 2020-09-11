<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use function resolve;

/**
 * @property mixed computer
 * @method static create(string[] $array)
 */
class ComputedValue extends Model
{
    protected $guarded = [];

    public function allowance()
    {
        return $this->morphOne(Allowance::class, 'valuable');
    }

    public function deduction()
    {
        return $this->morphOne(Deduction::class, 'valuable');
    }


    public function amount(Beneficiary $beneficiary = null)
    {
        if (! $beneficiary) {
            return Str::upper($this->computer);
        }

        return resolve($this->computer)->compute($beneficiary);
    }

    public function type()
    {
        return 'COMPUTED';
    }
}
