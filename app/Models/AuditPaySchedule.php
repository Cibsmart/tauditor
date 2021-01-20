<?php

namespace App\Models;

use Carbon\Carbon;
use App\Traits\CanBeReported;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use function number_format;

/**
 *
 * @method static schedules(\Illuminate\Database\Eloquent\Builder|Model|object $payroll, $type)
 * @method static where(string $string, $verification_number)
 * @method static allSchedules()
 */
class AuditPaySchedule extends Model
{
    use SoftDeletes, CanBeReported;

    protected $guarded = [];

    protected $casts = [
        'paid'       => 'boolean',
        'pension'    => 'boolean',
        'allowances' => 'array',
        'deductions' => 'array',
        'month'      => 'datetime'
    ];

    //A polymorphic relationship to either Bank or Microfinance
    public function bankable() : MorphTo
    {
        return $this->morphTo();
    }

    public function auditReports()
    {
        return $this->morphMany(AuditReport::class, 'reportable');
    }

    public function auditSubMdaSchedule()
    {
        return $this->belongsTo(AuditSubMdaSchedule::class);
    }

    public function auditPayroll()
    {
        return $this->auditSubMdaSchedule->auditMdaSchedule->auditPayrollCategory->auditPayroll;
    }

    public function auditPayrollCategory()
    {
        return $this->auditSubMdaSchedule->auditMdaSchedule->auditPayrollCategory;
    }

    public function auditMdaSchedule()
    {
        return $this->auditSubMdaSchedule->auditMdaSchedule;
    }

    public function domain()
    {
        return $this->auditSubMdaSchedule->auditMdaSchedule->auditPayrollCategory->auditPayroll->domain;
    }

    public function scopeAllSchedules($query)
    {
        return $query->join('audit_sub_mda_schedules', 'audit_pay_schedules.audit_sub_mda_schedule_id', '=', 'audit_sub_mda_schedules.id')
                     ->join('audit_mda_schedules', 'audit_sub_mda_schedules.audit_mda_schedule_id', '=', 'audit_mda_schedules.id')
                     ->join('audit_payroll_categories', 'audit_mda_schedules.audit_payroll_category_id', '=', 'audit_payroll_categories.id')
                     ->join('audit_payrolls', 'audit_payroll_categories.audit_payroll_id', '=', 'audit_payrolls.id');
    }

    public static function scopeSchedules($query, $payroll, $staff_type)
    {
        return $query->join('audit_sub_mda_schedules', 'audit_pay_schedules.audit_sub_mda_schedule_id', '=', 'audit_sub_mda_schedules.id')
                      ->join('audit_mda_schedules', 'audit_sub_mda_schedules.audit_mda_schedule_id', '=', 'audit_mda_schedules.id')
                      ->join('audit_payroll_categories', 'audit_mda_schedules.audit_payroll_category_id', '=', 'audit_payroll_categories.id')
                      ->join('audit_payrolls', 'audit_payroll_categories.audit_payroll_id', '=', 'audit_payrolls.id')
                      ->where('audit_payrolls.id', '=', $payroll->id)
                      ->where('audit_payroll_categories.staff_type', '=', $staff_type);
    }

    public function scopeOrderByMonth($query)
    {
        return $query->orderByRaw('date_format(audit_pay_schedules.month, "%Y-%m") DESC');
    }

    public function scopeWhereMonthLessThan($query, $date)
    {
        return $query->whereRaw('date_format(month, "%Y-%m") < ?', [$date->format('Y-m')]);
    }

    public function setAccountNumberAttribute($value)
    {
        return $this->attributes['account_number'] = self::pad($value, 10);
    }

    public function setBankCodeAttribute($value)
    {
        return $this->attributes['bank_code'] = self::pad($value, 3);
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

    public function getFormattedNetPayAttribute()
    {
        return number_format($this->net_pay, 2, '.', ',');
    }

    public function getPaymentDateAttribute()
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $this->timestamp)->format('d-m-Y H:i:s+0000');
    }

    protected static function pad($string, $padding)
    {
        return is_int($string) ? str_pad($string, $padding, '0', STR_PAD_LEFT) : $string;
    }


}
