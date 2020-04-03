<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed month
 * @property mixed year
 * @property  domain_id
 */
class Payroll extends Model
{
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function generatePaySchedule()
    {
        //Get a list of all active beneficiary

        //For each beneficiary
        //1. Basic Pay
        //2. Allowances
        //3. Deduction

        //Then save in pay_schedules table
    }
}
