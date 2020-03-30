<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use phpDocumentor\Reflection\Types\Float_;
use phpDocumentor\Reflection\Types\Mixed_;
use phpDocumentor\Reflection\Types\Integer;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use function is_null;
use function in_array;
use function array_merge;

/**
 * @property int id
 * @property string verification_number
 * @property string name
 * @property string last_name
 * @property string first_name
 * @property string middle_name
 * @property boolean active
 *
 * @property mixed salaryDetail
 * @property mixed bankDetail
 * @property mixed mdaDetail
 * @property mixed workDetail
 * @property mixed beneficiaryType
 * @property mixed domain
 * @property mixed structure
 */

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
        return $this->belongsTo(BeneficiaryType::class)->withDefault();
    }

    public function salaryStructure() : BelongsTo
    {
        return $this->belongsTo(Structure::class);
    }

    public function qualifications() : HasMany
    {
        return $this->hasMany(Qualification::class);
    }

    public function domain() : BelongsTo
    {
        return $this->belongsTo(Domain::class)->withDefault();
    }



    /*
    |-------------------------------------------------------------------------------
    | Methods
    |-------------------------------------------------------------------------------
    */
    public function basic() : float
    {
        return $this->salaryDetail->basicPay();
    }

    public function accountNumber() : string
    {
        return $this->bankDetail->account_number;
    }

    public function bankName() : string
    {
        return $this->bankDetail->bankable->name;
    }

    public function mdaName() : string
    {
        return $this->mdaDetail->mda->name;
    }

    public function subMdaName() : ?string
    {
        return $this->mdaDetail->subMda->name;
    }

    public function subSubMdaName() : ?string
    {
        return $this->mdaDetail->subSubMda->name;
    }

    public function designationName() : string
    {
        return $this->workDetail->designation->name;
    }

    public function gradeLevelName() : ?string
    {
        return $this->salaryDetail->payable->gradeLevel()->name;
    }

    public function stepName() : ?string
    {
        return $this->salaryDetail->payable->step()->name;
    }

    public function isPensioner() : bool
    {
        return in_array($this->beneficiaryType->code, ['ANPEN', 'LGPEN']);
    }

    /**
     * Set Beneficiaries Basic Pay
     * @param  int  $type
     * @param  int  $value
     * @return Beneficiary
     */
    public function setBasic(int $type, int $value) : Beneficiary
    {
        $payable = $type === 1
            ? new PersonalizedSalary(['monthly_basic' => $value])
            : new StructuredSalary(['cadre_step_id' => $value]) ;

        $salary = $this->salaryDetail->create();

        $payable->salary()->save($salary);

        return $this;
    }

    /**
     * Apply an Allowance to a Beneficiary
     * @param  Allowance  $allowance
     * @param  int|null  $allowable_id
     * @return Beneficiary
     */
    public function applyAllowance(Allowance $allowance, int $allowable_id = null) : Beneficiary
    {
        $attributes = [
            'amount' => $allowance->amount($this->basic()),
            'allowance_id' => $allowance->id,
        ];

        $attributes = $allowable_id
            ? array_merge($attributes, ['allowable_id' => $allowable_id])
            : $attributes;

        $this->allowanceDetails()->firstOrCreate($attributes);

        return $this;
    }

    /**
     * Remove an Allowance from a Beneficiary
     * @param  AllowanceDetail  $allowance_detail
     * @return Beneficiary
     */
    public function removeAllowance(AllowanceDetail $allowance_detail) : Beneficiary
    {
        $allowance_detail->unapply();

        return $this->fresh();
    }

    public function allowances() : Collection
    {
        return $this->allowanceDetails()->get()
                           ->load(['allowance.allowance_name'])
                           ->transform(fn($allowance) => [
                               'id' => $allowance->id,
                               'name' => $allowance->allowance->allowanceName->name,
                               'amount' => $allowance->amount,
                           ]);
    }

    /**
     * @param  Deduction  $deduction
     * @param  Deductible  $deductible
     * @return Beneficiary
     */
    public function applyDeduction(Deduction $deduction, Deductible $deductible) : Beneficiary
    {
        $attributes = [
            'amount' => $deduction->amount($this->basic()),
            'deduction_id' => $deduction->id
        ];

        $attributes = $deductible
            ? array_merge($attributes, ['deductible_id' => $deductible->id])
            : $attributes;

        $this->deductionDetails()->create($attributes);

        return $this;
    }

    public function removeDeduction(DeductionDetail $deduction_detail) : Beneficiary
    {
        $deduction_detail->unapply();

        return $this->fresh();
    }

    public function deductions() : Collection
    {
        return $this->deductionDetails()->get()
                    ->load(['deduction.deduction_name'])
                    ->transform(fn($deduction) => [
                        'id' => $deduction->id,
                        'name' => $deduction->deduction->deductionName->name,
                        'amount' => $deduction->amount,
                    ]);
    }

    /**
     * Synchronize all Attachable Allowances to a Beneficiary
     * @return Beneficiary
     */
    public function syncAllowances() : Beneficiary
    {
        if($this->isPensioner()){
            return $this;
        }

        $this->domain->syncAllowancesTo($this);
//        $this->beneficiaryType->syncAllowancesTo($this);
//        $this->structure->syncAllowancesTo($this);
//        $this->cadre->syncAllowancesTo($this);
//        $this->cadreStep->syncAllowancesTo($this);
//        $this->mdaStructure->syncAllowancesTo($this);

        return $this;
    }

    /**
     * Get Beneficiary's Last, First and Middle Name as a single string
     * @return string
     */
    public function getNameAttribute() : string
    {
        return "{$this->last_name} {$this->first_name} {$this->middle_name}";
    }

    /**
     * Search Beneficiaries and Relationships
     * @param $query
     * @param  array  $filters
     */
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
//                      })->orWhereHas('salaryDetail', function ($query) use ($search) {
//                               $query->whereHasMorph('payable', [StructuredSalary::class], function ($query) use ($search) {
//                                   $query->whereHas('gradeLevel', fn($query) => where('name', 'like', '%' . $search . '%'));
//                                   $query->whereHas('step', fn($query) => where('name', 'like', '%' . $search . '%'));
//                               });
                      });
            });
        });
    }
}
