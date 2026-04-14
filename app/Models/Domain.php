<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int id
 * @property mixed code
 * @property mixed name
 * @method static find($domain)
 */
class Domain extends Model
{
    public $incrementing = false;

    protected $keyType = 'string';

    protected $guarded = [];

    protected $casts = [
        'group' => 'boolean'
    ];

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

    public function auditPayrolls()
    {
        return $this->hasMany(AuditPayroll::class);
    }

    public function microFinanceBanks()
    {
        return $this->hasMany(MicroFinanceBank::class);
    }

    public function payComms()
    {
        return $this->hasMany(PayComm::class);
    }

    public function potentialUser()
    {
        return $this->hasMany(PotentialUser::class);
    }

}
