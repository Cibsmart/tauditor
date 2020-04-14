<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property mixed id
 */
class PaySchedule extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'allowances' => 'array',
        'deductions' => 'array',
        'pensioner' => 'boolean',
    ];

    public function mda()
    {
        return $this->belongsTo(Mda::class);
    }

    public function payroll()
    {
        return $this->belongsTo(Payroll::class);
    }

    public function scopePensioner($query)
    {
        return $query->where('pensioner', 1);
    }

    public function scopeStaff($query)
    {
        return $query->where('pensioner', 0);
    }

    public function setNetPayAttribute(float $value) : int
    {
        return $this->attributes['net_pay'] = $value * 100;
    }

    public function getNetPayAttribute(int $value) : float
    {
        return $value / 100;
    }


    public function setBasicPayAttribute(float $value) : int
    {
        return $this->attributes['basic_pay'] = $value * 100;
    }

    public function getBasicPayAttribute(int $value) : float
    {
        return $value / 100;
    }


    public function setGrossPayAttribute(float $value) : int
    {
        return $this->attributes['gross_pay'] = $value * 100;
    }

    public function getGrossPayAttribute(int $value) : float
    {
        return $value / 100;
    }

    public function setTotalAllowanceAttribute(float $value) : int
    {
        return $this->attributes['total_allowance'] = $value * 100;
    }

    public function getTotalAllowanceAttribute(int $value) : float
    {
        return $value / 100;
    }

    public function setTotalDeductionAttribute(float $value) : int
    {
        return $this->attributes['total_deduction'] = $value * 100;
    }

    public function getTotalDeductionAttribute(int $value) : float
    {
        return $value / 100;
    }

    public function getTotalAmountAttribute(int $value) : float
    {
        return $value / 100;
    }
}
