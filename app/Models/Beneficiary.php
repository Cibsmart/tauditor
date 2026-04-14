<?php

namespace App\Models;

use App\Casts\AddressCast;
use function array_merge;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

/**
 * @property int id
 * @property string verification_number
 * @property string name
 * @property string last_name
 * @property string first_name
 * @property string middle_name
 * @property mixed salaryDetail
 * @property mixed bankDetail
 * @property mixed mdaDetail
 * @property mixed workDetail
 * @property mixed beneficiaryType
 * @property mixed domain
 * @property mixed structure
 * @property mixed status
 * @property mixed pensioner
 * @property mixed allowanceDetails
 * @method static create($validate)
 */
class Beneficiary extends Model
{
    protected $guarded = [];

    protected $casts = [
        'date_of_birth' => 'date',
        'address'       => AddressCast::class,
    ];

    /*
    |-------------------------------------------------------------------------------
    | Relationships
    |-------------------------------------------------------------------------------
    */

    public function info()
    {
        return $this->hasOne(StaffInfo::class);
    }

    public function bankDetail() : HasOne
    {
        return $this->hasOne(BankDetail::class);
    }

    public function mandate()
    {
        return $this->hasMany(LoanMandate::class);
    }

    public function getAddressAttribute()
    {
        return $this->address_line_1
            ? "{$this->address_line_1} {$this->address_line_2}, {$this->address_city}, {$this->address_state}"
            : null;
    }

    public function getDobAttribute()
    {
        return $this->date_of_birth->format('d-m-Y H:i:s+0000');
    }

    public function bvn()
    {
        return $this->bankDetail->bvn;
    }

    public function status()
    {
        return $this->hasOne(BeneficiaryStatus::class);
    }

    public function gender() : BelongsTo
    {
        return $this->belongsTo(Gender::class);
    }

    public function mdaDetail() : HasOne
    {
        return $this->hasOne(MdaDetail::class);
    }

    public function salaryDetail() : HasOne
    {
        return $this->hasOne(SalaryDetail::class)->withDefault();
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

    public function isActive() : bool
    {
        return $this->status->active;
    }

    public function isPensioner() : bool
    {
        return $this->pensioner;
    }

    public function basic() : float
    {
        return $this->salaryDetail->basicPay();
    }

    public function totalMonthlyAllowance() : float
    {
        return $this->allowanceDetails->sum('amount');
    }

    public function totalMonthlyDeduction() : float
    {
        return $this->deductionDetails->sum('amount');
    }

    public function accountNumber() : ?string
    {
        return $this->bankDetail->account_number ?? null;
    }

    public function bankName() : ?string
    {
        return $this->bankDetail->bankable->name ?? null;
    }

    public function bank()
    {
        return $this->bankDetail->bankable();
    }

    public function mdaName() : ?string
    {
        return $this->mdaDetail->mda->name ?? null;
    }

    public function subMdaName() : ?string
    {
        return $this->mdaDetail->subMda->name ?? null;
    }

    public function subSubMdaName() : ?string
    {
        return $this->mdaDetail->subSubMda->name ?? null;
    }

    public function designationName() : ?string
    {
        return $this->workDetail->designation->name ?? null;
    }

    public function gradeLevelName() : ?string
    {
        if ($this->has('salaryDetail')->doesntExist()) {
            return null;
        }

        return $this->salaryDetail->payable->gradeLevel()->name;
    }

    public function stepName() : ?string
    {
        if ($this->has('salaryDetail')->doesntExist()) {
            return null;
        }

        return $this->salaryDetail->payable->step()->name;
    }

    /**
     * Set Beneficiaries Basic Pay
     * @param  int  $type
     * @param  int  $value
     * @return Beneficiary
     */
    public function setBasic(int $type, int $value) : self
    {
        $payable = $type === 1
            ? new PersonalizedSalary(['monthly_basic' => $value])
            : new StructuredSalary(['cadre_step_id' => $value]);

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
    public function applyAllowance(Allowance $allowance, int $allowable_id = null) : self
    {
        $attributes = [
            'amount'       => $allowance->amount($this),
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
    public function removeAllowance(AllowanceDetail $allowance_detail) : self
    {
        $allowance_detail->unapply();

        return $this->fresh();
    }

    public function allowances() : Collection
    {
        return $this->allowanceDetails()->get()
                    ->load(['allowance.allowanceName'])
                    ->transform(fn (AllowanceDetail $allowance) => [
                        'id'     => $allowance->id,
                        'name'   => $allowance->allowance->allowanceName->name,
                        'amount' => $allowance->amount,
                    ]);
    }

    /**
     * @param  Deduction  $deduction
     * @param  int  $deductible
     * @return Beneficiary
     */
    public function applyDeduction(Deduction $deduction, int $deductible = null) : self
    {
        $attributes = [
            'amount'       => $deduction->amount($this),
            'deduction_id' => $deduction->id,
        ];

        $attributes = $deductible
            ? array_merge($attributes, ['deductible_id' => $deductible->id])
            : $attributes;

        $this->deductionDetails()->create($attributes);

        return $this;
    }

    public function removeDeduction(DeductionDetail $deduction_detail) : self
    {
        $deduction_detail->unapply();

        return $this->fresh();
    }

    public function deductions() : Collection
    {
        return $this->deductionDetails()->get()
                    ->load(['deduction.deductionName'])
                    ->transform(fn (DeductionDetail $deduction) => [
                        'id'     => $deduction->id,
                        'name'   => $deduction->deduction->deductionName->name,
                        'amount' => $deduction->amount,
                    ]);
    }

    /**
     * Get Beneficiary's Last, First and Middle Name as a single string
     * @return string
     */
    public function getNameAttribute() : string
    {
        return "{$this->last_name} {$this->first_name} {$this->middle_name}";
    }

    public function setLastNameAttribute(string $value) : string
    {
        return $this->attributes['last_name'] = Str::upper($value);
    }

    public function setFirstNameAttribute(string $value) : string
    {
        return $this->attributes['first_name'] = Str::upper($value);
    }

    public function setMiddleNameAttribute(string $value = null) : ?string
    {
        return $this->attributes['middle_name'] = Str::upper($value) ?? null;
    }

    public function scopeActive($query)
    {
        return $query->where(function ($query) {
            $query->whereHas('status', fn ($query) => $query->where('active', 1));
        });
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
                      ->orWhereHas('status', function ($query) use ($search) {
                          $query->where('active', Str::startsWith($search, ['act', 'acti', 'activ', 'active']));
                      })->orWhereHas('mdaDetail', function ($query) use ($search) {
                          $query->whereHas('mda', fn ($query) => $query->where('name', 'like', '%' . $search . '%'));
                          $query->orWhereHas('subMda', fn ($query) => $query->where('name', 'like', '%' . $search . '%'));
                          $query->orWhereHas(
                              'subSubMda',
                              fn ($query) => $query->where('name', 'like', '%' . $search . '%')
                          );
                      })->orWhereHas('workDetail', function ($query) use ($search) {
                          $query->whereHas(
                              'designation',
                              fn ($query) => $query->where('name', 'like', '%' . $search . '%')
                          );
                      })->orWhereHas('bankDetail', function ($query) use ($search) {
                          $query->where('account_number', 'like', '%' . $search . '%')
                              ->orWhereHasMorph(
                                  'bankable',
                                  [Bank::class, MicroFinanceBank::class],
                                  fn ($query) => $query->where('name', 'like', '%' . $search . '%')
                              );
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
