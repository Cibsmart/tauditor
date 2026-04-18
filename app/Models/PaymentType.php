<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaymentType extends Model
{
    use HasFactory;
    public $incrementing = false;

    protected $keyType = 'string';

    public function beneficiaryTypes()
    {
        return $this->belongsToMany(self::class);
    }
}
