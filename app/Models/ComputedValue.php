<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use function resolve;

/**
 * @property mixed computer
 * @method static create(string[] $array)
 */
class ComputedValue extends Model
{
    protected $guarded = [];

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
