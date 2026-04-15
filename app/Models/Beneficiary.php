<?php

namespace App\Models;

use App\Casts\AddressCast;
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
 * @property mixed workDetail
 * @property mixed beneficiaryType
 * @property mixed domain
 * @property mixed structure
 * @property mixed status
 * @property mixed pensioner
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

    public function salaryDetail() : HasOne
    {
        return $this->hasOne(SalaryDetail::class)->withDefault();
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
                      });
            });
        });
    }
}
