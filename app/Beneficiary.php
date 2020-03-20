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

    public function next_of_kin()
    {
        return $this->hasOne(NextOfKin::class);
    }

    public function getNameAttribute()
    {
        return "{$this->last_name} {$this->first_name} {$this->middle_name}";
    }
}
