<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class PotentialUser extends Model
{
    use HasFactory, Notifiable, SoftDeletes;

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

    public function getNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }
}
