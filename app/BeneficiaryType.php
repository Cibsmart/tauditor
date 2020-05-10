<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @property mixed code
 */
class BeneficiaryType extends Model
{
    protected $guarded = [];

    public $incrementing = false;

    protected $keyType = 'string';

    protected $casts = [
        'pensioners' => 'boolean',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function designations() : HasMany
    {
        return $this->hasMany(Designation::class);
    }

    public function mdas() : HasMany
    {
        return $this->hasMany(Mda::class);
    }

    public function allowables() : MorphMany
    {
        return $this->morphMany(Allowable::class, 'allowable');
    }

    public function paymentTypes()
    {
        return $this->belongsToMany(PaymentType::class);
    }

    public function paymentCredential()
    {
        return $this->hasOne(PaymentCredential::class);
    }

    public function scopeThatReceives($query, $payment_type)
    {
        return $query->whereHas('paymentTypes', function ($query) use ($payment_type) {
            $query->where('payment_type_id', $payment_type);
        });
    }

    /**
     * Synchronize all BeneficiaryType Allowances to a Beneficiary
     * @param  Beneficiary  $beneficiary
     * @return Beneficiary
     */
    public function syncAllowancesTo(Beneficiary $beneficiary) : Beneficiary
    {
        $allowables = $this->allowables;

        foreach ($allowables as $allowable) {
            $beneficiary->applyAllowance($allowable->allowance, $allowable->id);
        }

        return $beneficiary;
    }
}
