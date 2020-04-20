<?php


namespace App\Audit;


use App\AuditPaySchedule;

class Analyse
{

    protected $checks = [
        CheckNewBeneficiary::class,
        CheckAccountNumber::class,
    ];

    public function check(AuditPaySchedule $schedule)
    {
        foreach ($this->checks as $check)
        {
            app($check)->check($schedule);
        }
    }
}
