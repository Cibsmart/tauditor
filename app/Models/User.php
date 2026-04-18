<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property mixed id
 * @property mixed domain
 * @property mixed last_name
 * @property mixed first_name
 * @property mixed email
 * @method static create(array $array)
 * @method static find(int $int)
 */
class User extends Authenticatable
{
    use HasFactory;
    use HasApiTokens, Notifiable, HasRoles, SoftDeletes;

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

    public function microfinanceBank()
    {
        return $this->hasOne(UserMicroFinanceBank::class);
    }

    public function requests()
    {
        return $this->hasMany(RequestLog::class);
    }

    public function setPasswordAttribute(string $value) : string
    {
        return $this->attributes['password'] = Hash::make($value);
    }

    public function getNameAttribute() : string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function beneficiaries()
    {
        return $this->domain->beneficiaries();
    }

    public function auditPayrolls()
    {
        return $this->domain->auditPayrolls();
    }

    public function metas()
    {
        return $this->morphMany(Meta::class, 'metable');
    }

    public function getMetas($key)
    {
        return $this->metas()->where('name', $key)->get();
    }
}
