<?php

namespace App;

use App\Beneficiary;
use Illuminate\Database\Eloquent\Model;

class Domain extends Model
{
    protected $guarded = [];

    public function employees()
    {
        return $this->hasMany(Beneficiary::class);
    }

    public function staff_types()
    {
        return $this->hasMany(BeneficiaryType::class);
    }
}
