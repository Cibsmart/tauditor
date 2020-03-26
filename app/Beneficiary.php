<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
    public function gender() : BelongsTo
    {
        return $this->belongsTo(Gender::class);
    }

    public function bankDetail() : HasOne
    {
        return $this->hasOne(BankDetail::class);
    }

    public function mdaDetail() : HasOne
    {
        return $this->hasOne(MdaDetail::class);
    }

    public function salaryDetail() : HasOne
    {
        return $this->hasOne(SalaryDetail::class);
    }

    public function allowanceDetails() : HasMany
    {
        return $this->hasMany(AllowanceDetail::class);
    }

    public function deductionDetails() : HasMany
    {
        return $this->hasMany(DeductionDetail::class);
    }

    public function workDetail() : HasOne
    {
        return $this->hasOne(WorkDetail::class);
    }

    public function nextOfKin() : HasOne
    {
        return $this->hasOne(NextOfKin::class);
    }

    public function beneficiaryType() : BelongsTo
    {
        return $this->belongsTo(BeneficiaryType::class);
    }

    public function qualifications() : HasMany
    {
        return $this->hasMany(Qualification::class);
    }

    public function domain() : BelongsTo
    {
        return $this->belongsTo(Domain::class);
    }



    /*
    |-------------------------------------------------------------------------------
    | Methods
    |-------------------------------------------------------------------------------
    */
    public function basic() : int
    {

    }

    public function setBasic($type, $value) : Beneficiary
    {
        $payable = $type === 1
            ? new PersonalizedSalary(['monthly_basic' => $value])
            : new StructuredSalary(['structure_grade_level_step_id' => $value]) ;

        $salary = $this->salaryDetail->create();

        $payable->salary()->save($salary);

        return $this;
    }

    public function applyAllowance(Allowance $allowance) : Beneficiary
    {
        $this->allowance_details()->create([
            'amount' => $allowance->amount(5000),
            'allowance_id' => $allowance->id
        ]);

        return $this;
    }

    public function removeAllowance(AllowanceDetail $allowance_detail) : Beneficiary
    {
        $allowance_detail->unapply();

        return $this->fresh();
    }

    public function allowances() : Collection
    {
        return $this->allowance_details()->get()
                           ->load(['allowance.allowance_name'])
                           ->transform(fn($allowance) => [
                               'id' => $allowance->id,
                               'name' => $allowance->allowance->allowance_name->name,
                               'amount' => $allowance->amount,
                           ]);
    }

    public function applyDeduction(Deduction $deduction) : Beneficiary
    {
        $this->deduction_details()->create([
            'amount' => $deduction->amount(5000),
            'deduction_id' => $deduction->id
        ]);

        return $this;
    }

    public function removeDeduction(DeductionDetail $deduction_detail) : Beneficiary
    {
        $deduction_detail->unapply();

        return $this->fresh();
    }

    public function deductions() : Collection
    {
        return $this->deduction_details()->get()
                    ->load(['deduction.deduction_name'])
                    ->transform(fn($deduction) => [
                        'id' => $deduction->id,
                        'name' => $deduction->deduction->deduction_name->name,
                        'amount' => $deduction->amount,
                    ]);
    }

    public function getNameAttribute() : string
    {
        return "{$this->last_name} {$this->first_name} {$this->middle_name}";
    }

    public function scopeFilters($query, array $filters) : void
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('last_name', 'like', '%' . $search . '%')
                      ->orWhere('first_name', 'like', '%' . $search . '%')
                      ->orWhere('middle_name', 'like', '%' . $search . '%')
                      ->orWhere('verification_number', 'like', '%' . $search . '%')
                      ->orWhereHas('mdaDetail', function($query) use ($search){
                          $query->whereHas('mda', fn($query) => $query->where('name', 'like', '%' . $search . '%'));
                          $query->orWhereHas('subMda', fn($query) => $query->where('name', 'like', '%' . $search . '%'));
                          $query->orWhereHas('subSubMda', fn($query) => $query->where('name', 'like', '%' . $search . '%'));
                      })->orWhereHas('workDetail', function($query) use ($search){
                          $query->whereHas('designation', fn($query) => $query->where('name', 'like', '%' . $search . '%'));
                      })->orWhereHas('bankDetail', function ($query) use ($search) {
                          $query->where('account_number', 'like', '%' . $search . '%')
                               ->orWhereHasMorph('bankable',
                                   [Bank::class, MicroFinanceBank::class],
                                   fn ($query) => $query->where('name', 'like', '%' . $search . '%'));
                      });
            });
        });
    }
}
