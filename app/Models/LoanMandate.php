<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanMandate extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'disbursement_date' => 'datetime',
        'collection_date' => 'datetime',
    ];

    public function beneficiary()
    {
        return $this->belongsTo(Beneficiary::class);
    }

    public function setLoanAmountAttribute($value)
    {
        return $this->attributes['loan_amount'] = $value * 100;
    }

    public function getLoanAmountAttribute($value)
    {
        return $value / 100;
    }

    public function setCollectionAmountAttribute($value)
    {
        return $this->attributes['collection_amount'] = $value * 100;
    }

    public function getCollectionAmountAttribute($value)
    {
        return $value / 100;
    }

    public function setTotalCollectionAmountAttribute($value)
    {
        return $this->attributes['total_collection_amount'] = $value * 100;
    }

    public function getTotalCollectionAmountAttribute($value)
    {
        return $value / 100;
    }

    public function cancel()
    {
        $this->status = 'C';

        $this->save();
    }
}
