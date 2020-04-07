<?php

namespace App;

use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * @property mixed domain
 */
class User extends Authenticatable
{
    use Notifiable, HasRoles, SoftDeletes;

    protected $guarded = [];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function domain() : BelongsTo
    {
        return $this->belongsTo(Domain::class);
    }

    public function setPasswordAttribute(string $value) : string
    {
        return $this->attributes['password'] = Hash::make($value);
    }

    public function beneficiaries()
    {
        return $this->domain->beneficiaries();
    }

    public function allowances()
    {
        return $this->domain->allowances();
    }

    public function deductions()
    {
        return $this->domain->deductions();
    }

    public function deductionstype()
    {
        return $this->domain->deductionstype();
    }

    public function deductionsname()
    {
        return $this->domain->deductionsname();
    }
}
