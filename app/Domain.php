<?php

namespace App;

use App\Beneficiary;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Domain extends Model
{
    protected $guarded = [];

    public function beneficiaries() : HasMany
    {
        return $this->hasMany(Beneficiary::class);
    }

    public function beneficiaryTypes() : HasMany
    {
        return $this->hasMany(BeneficiaryType::class);
    }

    public function structures() : HasMany
    {
        return $this->hasMany(Structure::class);
    }
}
