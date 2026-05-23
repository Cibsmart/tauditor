<?php

namespace App\Jobs;

use App\Actions\AuditPayScheduleAction;
use App\Models\AuditSubMdaSchedule;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class AnalysePaySchedules implements ShouldQueue
{
    use Queueable;

    public int $tries = 5;

    public function __construct(public int $scheduleId) {}

    public function handle(AuditPayScheduleAction $schedule_action): void
    {
        $schedule = AuditSubMdaSchedule::find($this->scheduleId);

        // Under a burst dispatch the row can be momentarily invisible to this
        // worker; release and retry rather than failing. A row that is truly
        // gone still surfaces once $tries is exhausted.
        if ($schedule === null) {
            $this->release(5);

            return;
        }

        // A retry may land after a prior attempt already analysed the schedule.
        if ($schedule->analysed !== null) {
            return;
        }

        $schedule_action->execute($schedule);
    }
}
