<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AuditPayrollCategory extends Model
{
    protected $guarded = [];

    public function auditPayroll()
    {
        return $this->belongsTo(AuditPayroll::class);
    }

    public function auditMdaSchedules()
    {
        return $this->hasMany(AuditMdaSchedule::class);
    }

    public function paymentType()
    {
        return $this->belongsTo(PaymentType::class);
    }

    public function paymentTypeName()
    {
        return $this->paymentType->name;
    }
}
