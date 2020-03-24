<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Beneficiary extends Model
{
    protected $guarded = [];

    protected $casts = [
        'date_of_birth' => 'date',
        'active' => 'boolean',
        'address' => AddressCast::class,
    ];

    /*
    |-------------------------------------------------------------------------------
    | Relationships
    |-------------------------------------------------------------------------------
    */
    public function gender()
    {
        return $this->belongsTo(Gender::class);
    }

    public function bank()
    {
        return $this->hasOne(BankDetail::class);
    }

    public function mda_detail()
    {
        return $this->hasOne(MdaDetail::class);
    }

    public function salary_detail()
    {
        return $this->hasOne(SalaryDetail::class);
    }

    public function allowance_details()
    {
        return $this->hasMany(AllowanceDetail::class);
    }

    public function work_detail()
    {
        return $this->hasOne(WorkDetail::class);
    }

    public function next_of_kin()
    {
        return $this->hasOne(NextOfKin::class);
    }

    public function beneficiary_type()
    {
        return $this->belongsTo(BeneficiaryType::class);
    }

    public function qualifications()
    {
        return $this->hasMany(Qualification::class);
    }

    public function domain()
    {
        return $this->belongsTo(Domain::class);
    }



    /*
    |-------------------------------------------------------------------------------
    | Methods
    |-------------------------------------------------------------------------------
    */

    public function basic()
    {

    }

    public function applyAllowance(Allowance $allowance)
    {
        $this->allowance_details()->create([
            'amount' => $allowance->amount(5000),
            'allowance_id' => $allowance->id
        ]);

        return $this;
    }

    public function removeAllowance(AllowanceDetail $allowance_detail)
    {
        $allowance_detail->unapply();

        return $this->fresh();
    }

    public function allowances()
    {
        return $this->allowance_details()->get()
                           ->load(['allowance.allowance_name'])
                           ->transform(fn($allowance) => [
                               'id' => $allowance->id,
                               'name' => $allowance->allowance->allowance_name->name,
                               'amount' => $allowance->amount,
                           ]);
    }

    public function getNameAttribute()
    {
        return "{$this->last_name} {$this->first_name} {$this->middle_name}";
    }

    public function scopeFilters($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('last_name', 'like', '%' . $search . '%')
                      ->orWhere('first_name', 'like', '%' . $search . '%')
                      ->orWhere('middle_name', 'like', '%' . $search . '%')
                      ->orWhere('verification_number', 'like', '%' . $search . '%')
                      ->orWhereHas('mda_detail', function($query) use ($search){
                          $query->whereHas('mda', fn($query) => $query->where('name', 'like', '%' . $search . '%'));
                          $query->orWhereHas('sub_mda', fn($query) => $query->where('name', 'like', '%' . $search . '%'));
                          $query->orWhereHas('sub_sub_mda', fn($query) => $query->where('name', 'like', '%' . $search . '%'));
                      })->orWhereHas('work_detail', function($query) use ($search){
                          $query->whereHas('designation', fn($query) => $query->where('name', 'like', '%' . $search . '%'));
                      })->orWhereHas('bank', function ($query) use ($search) {
                          $query->where('account_number', 'like', '%' . $search . '%')
                               ->orWhereHasMorph('bankable',
                                   [Bank::class, MicroFinanceBank::class],
                                   fn ($query) => $query->where('name', 'like', '%' . $search . '%'));
                      });
            });
        });
    }
}
