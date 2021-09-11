<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use function collect;
use function optional;
use function str_getcsv;
use function number_format;

class LoanMandate extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'disbursement_date' => 'datetime',
        'collection_date' => 'datetime',
    ];

    protected $appends = ['color'];

    public function beneficiary()
    {
        return $this->belongsTo(Beneficiary::class);
    }

    public function loan_status()
    {
        return $this->belongsTo(LoanStatus::class, 'status');
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

    public function getFormattedLoanAmountAttribute()
    {
        return number_format($this->loan_amount,2);
    }

    public function getFormattedCollectionAmountAttribute()
    {
        return number_format($this->collection_amount,2);
    }

    public function getFormattedDisbursementDateAttribute()
    {
        return $this->disbursement_date->format('jS M Y');
    }

    public function cancel()
    {
        $this->status = 'C';

        $this->save();
    }

    public function getColorAttribute()
    {
        return
            [
                'A' => 'bg-red-100 text-red-800',
                'P' => 'bg-green-100 text-green-800',
            ][$this->status] ?? 'bg-gray-200 text-gray-800';
    }

    public function scopeSearch($query, string $terms = null)
    {
        collect(str_getcsv($terms, ' ', '"'))
            ->filter()
            ->each(function ($term) use ($query) {
                $term = "{$term}%";

                $query->whereIn('loan_mandates.id', function ($query) use ($term) {
                    $query->select('id')->from(function ($query) use ($term) {
                        $query->select('m.id as id')
                              ->from('loan_mandates as m')
                              ->join('loan_statuses as s', 'm.status', '=', 's.id')
                              ->join('beneficiaries as b', 'm.beneficiary_id', '=', 'b.id')
                              ->where('m.staff_id', 'like', $term)
                              ->orWhere('m.account_number', 'like', $term)
                              ->orWhere('m.reference', 'like', $term)
                              ->orWhere('m.bvn', 'like', $term)
                              ->orWhere('m.phone_number', 'like', $term)
                              ->orWhere('b.last_name', 'like', $term)
                              ->orWhere('b.first_name', 'like', $term)
                              ->orWhere('b.middle_name', 'like', $term)
                              ->orWhere('s.name', 'like', $term);
                    }, 'matches');
                });
            });
    }
}
