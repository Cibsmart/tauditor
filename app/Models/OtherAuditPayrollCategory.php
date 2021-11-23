<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use function now;

class OtherAuditPayrollCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'uploaded' => 'datetime',
        'paycomm_tenece' => 'boolean',
        'paycomm_fidelity' => 'boolean',
        'autopay_uploaded' => 'datetime',
        'autopay_generated' => 'datetime',
    ];

    public function auditPayroll()
    {
        return $this->belongsTo(AuditPayroll::class);
    }

    public function auditOtherPaySchedules()
    {
        return $this->hasMany(AuditOtherPaySchedule::class);
    }

    public function paymentType()
    {
        return $this->belongsTo(PaymentType::class);
    }

    public function paymentTypeName()
    {
        return $this->paymentType->name;
    }

    public function setTotalNetPayAttribute(float $value) : int
    {
        return $this->attributes['total_net_pay'] = $value * 100;
    }

    public function setPaymentTitleAttribute(string $value) : string
    {
        return $this->attributes['payment_title'] = Str::upper($value);
    }

    public function getTotalNetPayAttribute(?int $value = 0) : float
    {
        return $value / 100;
    }

    public function getColorAttribute()
    {
        return [
                   'all' => 'bg-blue-100 text-blue-800',
                   'nys' => 'bg-green-100 text-green-800',
                   'arr' => 'bg-pink-100 text-pink-800',
               ][$this->payment_type_id] ?? 'bg-gray-200 text-gray-800';
    }

    public function scheduleUploaded($file_path)
    {
        $this->uploaded = now();
        $this->user_id = Auth::id();
        $this->file_path = $file_path;
        $this->total_net_pay = $this->auditOtherPaySchedules()->sum('amount') / 100;
        $this->head_count = $this->auditOtherPaySchedules()->count();

        $this->save();
    }
}
