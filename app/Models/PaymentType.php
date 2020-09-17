<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentType extends Model
{
    public $incrementing = false;

    protected $keyType = 'string';

    public function beneficiaryTypes()
    {
        return $this->belongsToMany(PaymentType::class);
    }
}
