<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Beneficiary extends Model
{
    protected $guarded = [];

    protected $casts = [
        'date_of_birth' => 'date',
        'active' => 'boolean'
    ];

    public function gender()
    {
        return $this->belongsTo(Gender::class);
    }

    public function bank()
    {
        return $this->hasOne(BankDetail::class);
    }

    public function getNameAttribute()
    {
        return "{$this->last_name} {$this->first_name} {$this->middle_name}";
    }
}
