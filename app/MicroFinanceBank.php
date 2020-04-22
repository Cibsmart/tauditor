<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MicroFinanceBank extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    public function beneficiaries() : MorphMany
    {
        return $this->morphMany(BankDetail::class, 'bankable');
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
