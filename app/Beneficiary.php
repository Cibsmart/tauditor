<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Beneficiary extends Model
{
    protected $guarded = [];

    protected $casts = [
        'date_of_birth' => 'date',
        'active' => 'boolean',
        'address' => AddressCast::class,
    ];

    public function gender()
    {
        return $this->belongsTo(Gender::class);
    }

    public function bank()
    {
        return $this->hasOne(BankDetail::class);
    }

    public function mda_detail()
    {
        return $this->hasOne(MdaDetail::class);
    }

    public function work_detail()
    {
        return $this->hasOne(WorkDetail::class);
    }

    public function next_of_kin()
    {
        return $this->hasOne(NextOfKin::class);
    }

    public function beneficiary_type()
    {
        return $this->belongsTo(BeneficiaryType::class);
    }

    public function qualifications()
    {
        return $this->hasMany(Qualification::class);
    }

    public function domain()
    {
        return $this->belongsTo(Domain::class);
    }

    public function getNameAttribute()
    {
        return "{$this->last_name} {$this->first_name} {$this->middle_name}";
    }
}
