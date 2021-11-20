<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @method static where(string $string, string $string1, string $string2)
 */
class MicroFinanceBank extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    public function beneficiaries() : MorphMany
    {
        return $this->morphMany(BankDetail::class, 'bankable');
    }

    public function domain()
    {
        return $this->belongsTo(Domain::class);
    }

    public function bank() : BelongsTo
    {
        return $this->belongsTo(Bank::class);
    }

    public function mfbSchedules()
    {
        return $this->hasMany(MicrofinanceBankSchedule::class);
    }

    public function bankableType()
    {
        return 'micro_finance';
    }

    public function bankCode()
    {
        return $this->bank->code;
    }
}
