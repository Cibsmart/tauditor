<?php


namespace App\Audit;


use App\AuditPaySchedule;
use App\Contracts\Auditable;

class AccountNumberAudit implements Auditable
{

    public function check(AuditPaySchedule $schedule)
    {
        // TODO: Implement check() method.
    }

    public function report()
    {
        // TODO: Implement report() method.
    }
}
