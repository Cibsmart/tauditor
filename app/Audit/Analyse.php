<?php

namespace App\Audit;

use App\Models\AuditPaySchedule;

class Analyse
{
    protected array $checks = [
        CheckNewBeneficiary::class,
        CheckAccountNumber::class,
        CheckBankName::class,
        CheckBasicPay::class,
        CheckGrossPay::class,
        CheckNetPay::class,
        CheckTotalAllowance::class,
        CheckAllowances::class,
        CheckTotalDeduction::class,
        CheckDeductions::class,
    ];

    public function check(AuditPaySchedule $schedule)
    {
        foreach ($this->checks as $check) {
            app($check)->check($schedule);
        }
    }
}
