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

    public function beneficiaryTypes()
    {
        return $this->hasMany(BeneficiaryType::class);
    }

    public function structures()
    {
        return $this->hasMany(Structure::class);
    }
}
