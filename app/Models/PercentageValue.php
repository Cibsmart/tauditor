<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use function number_format;

/**
 * @property mixed percentage
 * @method static create(array $array)
 */
class PercentageValue extends Model
{
    protected $guarded = [];

    public function deduction()
    {
        return $this->morphOne(Deduction::class, 'valuable');
    }

    public function amount(Beneficiary $beneficiary = null) : float
    {
        if (! $beneficiary) {
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
