<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AuditPaySchedule extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'paid' => 'boolean',
        'allowances' => 'array',
        'deductions' => 'array',
    ];

    public function auditSubMdaSchedule()
    {
        return $this->belongsTo(AuditSubMdaSchedule::class);
    }

    public function setAccountNumberAttribute($value)
    {
        return $this->attributes['account_number'] = $this->pad($value, 10);
    }

    public function setBankCodeAttribute($value)
    {
        return $this->attributes['bank_code'] = $this->pad($value, 3);
    }

    public function setBasicPayAttribute(float $value) : int
    {
        return $this->attributes['basic_pay'] = $value * 100;
    }

    public function getBasicPayAttribute(int $value) : float
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

    public function setGrossPayAttribute(float $value) : int
    {
        return $this->attributes['gross_pay'] = $value * 100;
    }

    public function getGrossPayAttribute(int $value) : float
    {
        return $value / 100;
    }

    public function setNetPayAttribute(float $value) : int
    {
        return $this->attributes['net_pay'] = $value * 100;
    }

    public function getNetPayAttribute(int $value) : float
    {
        return $value / 100;
    }


    protected static function pad($string, $padding)
    {
        return is_int($string) ? str_pad($string, $padding, '0', STR_PAD_LEFT) : $string;
    }
}
