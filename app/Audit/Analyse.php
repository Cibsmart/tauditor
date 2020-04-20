<?php


namespace App\Audit;


use App\AuditPaySchedule;

class Analyse
{

    protected $checks = [
        NewBeneficiaryAudit::class,
    ];

    public function check(AuditPaySchedule $schedule)
    {
        foreach ($this->checks as $check)
        {
            app($check)->check($schedule);
        }
    }
}
