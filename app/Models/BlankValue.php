<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use function number_format;

/**
 * @method static create()
 */
class BlankValue extends Model
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
        return null;
    }

    public function type()
    {
        return 'BLANK';
    }
}
