<?php

namespace App\Jobs;

use App\Actions\AuditPayScheduleAction;
use App\Models\AuditSubMdaSchedule;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class AnalysePaySchedules implements ShouldQueue
{
    use Queueable;

    public int $timeout = 0;

    public function __construct(public AuditSubMdaSchedule $schedule) {}

    public function handle(AuditPayScheduleAction $schedule_action)
    {
        $schedule_action->execute($this->schedule);
    }
}
