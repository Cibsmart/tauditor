<?php

namespace App\Models;

use App\Casts\AddressCast;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int id
 * @property string verification_number
 * @property string name
 * @property string last_name
 * @property string first_name
 * @property string middle_name
 * @property mixed bankDetail
 * @property mixed beneficiaryType
 * @property mixed domain
 * @property mixed pensioner
 * @method static create($validate)
 */
class Beneficiary extends Model
{
    use HasFactory;
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

    public function gender() : BelongsTo
    {
        return $this->belongsTo(Gender::class);
    }

    public function beneficiaryType() : BelongsTo
    {
        return $this->belongsTo(BeneficiaryType::class)->withDefault();
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

    public function isPensioner() : bool
    {
        return $this->pensioner;
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
                      ->orWhereHas('bankDetail', function ($query) use ($search) {
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
