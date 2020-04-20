<?php


namespace App\Actions;


use App\Audit\Analyse;
use App\AuditSubMdaSchedule;

class AuditPayScheduleAction
{

    public function execute(AuditSubMdaSchedule $sub_mda_schedule)
    {
        $schedules = $sub_mda_schedule->auditPaySchedules;

        foreach ($schedules as $schedule)
        {
            (new Analyse)->check($schedule);
        }
    }
}
