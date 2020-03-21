<?php

namespace App;

use App\Beneficiary;
use Illuminate\Database\Eloquent\Model;

class Domain extends Model
{
    protected $guarded = [];

    public function beneficiaries()
    {
        return $this->hasMany(Beneficiary::class);
    }

    public function beneficiary_types()
    {
        return $this->hasMany(BeneficiaryType::class);
    }
}
