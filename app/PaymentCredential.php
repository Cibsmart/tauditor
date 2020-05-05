<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaymentCredential extends Model
{

    public function beneficiaryType()
    {
        return $this->belongsTo(BeneficiaryType::class);
    }
}
