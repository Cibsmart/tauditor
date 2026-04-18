<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
