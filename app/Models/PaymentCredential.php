<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentCredential extends Model
{
    use HasFactory;

    protected $casts = [
        'is_single_debit' => 'boolean',
    ];

    public function beneficiaryType()
    {
        return $this->belongsTo(BeneficiaryType::class);
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }
}
