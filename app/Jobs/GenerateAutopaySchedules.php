<?php

namespace App\Jobs;

use App\Actions\GenerateAutoPayScheduleAction;
use App\Models\AuditSubMdaSchedule;
use App\Models\Domain;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class GenerateAutopaySchedules implements ShouldQueue
{
    use Queueable;

    public int $tries = 5;

    public function __construct(public Domain $domain, public int $scheduleId) {}

    public function handle(GenerateAutoPayScheduleAction $auto_pay_schedule_action): void
    {
        $schedule = AuditSubMdaSchedule::find($this->scheduleId);

        // Under a burst dispatch the row can be momentarily invisible to this
        // worker; release and retry rather than failing. A row that is truly
        // gone still surfaces once $tries is exhausted.
        if ($schedule === null) {
            $this->release(5);

            return;
        }

        // A retry may land after a prior attempt already generated the autopay;
        // skip to avoid creating duplicate schedule rows.
        if ($schedule->autopay_generated !== null) {
            return;
        }

        $auto_pay_schedule_action->execute($this->domain, $schedule);
    }
}
