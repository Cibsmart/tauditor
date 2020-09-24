<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @method static first($payroll)
 * @method static find($payroll)
 * @method whereRaw(string $string, array $array)
 * @method where(string $string, string $string1, string $string2)
 */
class AuditPayroll extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'timestamp'         => 'datetime',
        'analysed'          => 'boolean',
        'autopay_generated' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function domain() : BelongsTo
    {
        return $this->belongsTo(Domain::class);
    }

    public function auditPaymentCategories()
    {
        return $this->hasMany(AuditPayrollCategory::class);
    }

    public function auditMdaSchedules()
    {
        return $this->hasManyThrough(AuditMdaSchedule::class, AuditPayrollCategory::class);
    }

    public function createdBy()
    {
        return $this->user->name;
    }

    public function dateCreated()
    {
        return $this->created_at->timezone('Africa/Lagos')->diffForHumans();
    }

    public function setTotalNetPayAttribute(float $value) : int
    {
        return $this->attributes['total_net_pay'] = $value * 100;
    }

    public function getTotalNetPayAttribute(?int $value = 0) : float
    {
        return $value / 100;
    }

    public function totalNetPay()
    {
        return $this->auditPaymentCategories->sum('total_net_pay');
    }

    public function headCount()
    {
        return $this->auditPaymentCategories->sum('head_count');
    }

    public function scopeOrderByTimestamp($query)
    {
        return $query->orderByRaw('date_format(timestamp, "%Y-%m") DESC');
    }

    public function scopeWhereTimestampLessThan($query, $date, $domain_id)
    {
        return $query->where('domain_id', '=', $domain_id)
                     ->whereRaw('date_format(timestamp, "%Y-%m") < ?', [$date->format('Y-m')]);
    }

    public function scopePayrolls($query)
    {
        return $query->join('audit_payroll_categories', 'audit_payrolls.id', '=', 'audit_payroll_categories.audit_payroll_id')
                     ->join('audit_mda_schedules', 'audit_payroll_categories.id', '=', 'audit_mda_schedules.audit_payroll_category_id')
                     ->join('audit_sub_mda_schedules', 'audit_mda_schedules.id', '=', 'audit_sub_mda_schedules.audit_mda_schedule_id')
                     ->join('microfinance_bank_schedules', 'audit_sub_mda_schedules.id', '=', 'microfinance_bank_schedules.audit_sub_mda_schedule_id');
    }

    public function scopeOrderByMonth($query)
    {
        return $query->orderByRaw('date_format(audit_payrolls.timestamp, "%Y-%m") DESC');
    }

    public function previousPayroll($domain_id)
    {
        return $this->where('domain_id', '=', $domain_id)
                    ->whereRaw('date_format(timestamp, "%Y-%m") = ?', [$this->timestamp->subMonth()->format('Y-m')])->first();
    }

    public function month($abbreviation = false)
    {
        if (! $abbreviation) {
            return "$this->month_name $this->year";
        }

        $month = Str::of($this->month_name)->limit(3, '');
        $year = Str::of($this->year)->substr(2, 2);

        return "$month $year";
    }
}
