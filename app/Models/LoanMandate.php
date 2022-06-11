<?php

namespace App\Models;

use App\Notifications\MandateReceived;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notification;

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

    public function deductions()
    {
        return $this->hasMany(FidelityLoanDeduction::class);
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
        return number_format($this->loan_amount, 2);
    }

    public function getFormattedCollectionAmountAttribute()
    {
        return number_format($this->collection_amount, 2);
    }

    public function getFormattedDisbursementDateAttribute()
    {
        return $this->disbursement_date->format('jS M Y');
    }

    public function deductionCount()
    {
        return $this->deductions()->count();
    }

    public function isNotPaid()
    {
        return $this->number_of_repayments > $this->deductionCount();
    }

    public function isPaid()
    {
        return ($this->status === 'P') ||
            ($this->number_of_repayments === $this->deductionCount());
    }

    public function cancel()
    {
        $this->status = 'C';
        $this->processed = null;
        $this->save();
    }

    public function activate()
    {
        $this->status = 'A';
        $this->save();
    }

    public function markAsPaid()
    {
        $this->status = 'P';
        $this->save();
    }

    public function markAsProcessed()
    {
        $this->processed = now();
        $this->save();
    }

    public function process()
    {
        switch ($this->status) {
            case 'N':
                $this->activate();
                $this->markAsProcessed();
                break;
            case 'A':
            case 'P':
            case 'C':
                $this->markAsProcessed();
                break;
        }
    }

    public function getColorAttribute()
    {
        return
            [
                'A' => 'bg-blue-100 text-blue-800',
                'P' => 'bg-green-100 text-green-800',
                'N' => 'bg-pink-100 text-pink-800',
            ][$this->status] ?? 'bg-gray-200 text-gray-800';
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'A');
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $terms) {
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
        })->when($filters['status'] ?? null, function ($query, $status) {
            $query->where('status', $status);
        })->when($filters['processed'] ?? null, function ($query, $processed) {
            if ($processed === 'true') {
                $query->whereNotNull('processed');
            }

            if ($processed === 'false') {
                $query->whereNull('processed');
            }
        });
    }
}
