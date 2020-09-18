<?php

namespace App\Models;

use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PotentialUser extends Model
{
    use HasFactory, Notifiable;

    protected $guarded = [];

    protected $casts = [
        'email_sent' => 'datetime',
        'registered' => 'datetime',
    ];

    public function domain()
    {
        return $this->belongsTo(Domain::class);
    }

    public function microfinanceBank()
    {
        return $this->hasOne(PotentialUserMfb::class);
    }

    public function getNameAttribute() : string
    {
        return "{$this->first_name} {$this->last_name}";
    }
}
