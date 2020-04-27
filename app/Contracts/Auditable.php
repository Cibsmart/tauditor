<?php


namespace App\Contracts;

use App\AuditPaySchedule;

interface Auditable
{
    public function check(AuditPaySchedule $schedule);
}
