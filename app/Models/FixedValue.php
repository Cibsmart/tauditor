<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @method static create(array $array)
 */
class FixedValue extends Model
{
    protected $guarded = [];

    public function deduction()
    {
        return $this->morphOne(Deduction::class, 'valuable');
    }

    public function amount() : float
    {
        return $this->amount;
    }

    public function type()
    {
        return 'FIXED';
    }

    public function setAmountAttribute(float $value) : int
    {
        return $this->attributes['Amount'] = $value * 100;
    }

    public function getAmountAttribute(int $value) : float
    {
        return $value / 100;
    }
}
