<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BeneficiaryStatus extends Model
{
    protected $guarded = [];

    protected $casts = [
        'active' => 'boolean'
    ];

    public function beneficiary()
    {
        return $this->belongsTo(Beneficiary::class);
    }
}
