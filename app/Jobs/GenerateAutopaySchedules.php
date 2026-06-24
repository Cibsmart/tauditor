<?php

namespace App\Jobs;

use App\Actions\GenerateAutoPayScheduleAction;
use App\Models\AuditSubMdaSchedule;
use App\Models\Domain;
use DateTimeInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Throwable;

class GenerateAutopaySchedules implements ShouldQueue
{
    use Queueable;

    public int $timeout = 180;

    public function __construct(public Domain $domain, public int $scheduleId) {}

    // Bound retries on wall-clock instead of attempt count so a worker
    // restart or release-on-missing-row doesn't burn the budget; must
    // be < redis retry_after (210s) to avoid concurrent re-issue.
    public function retryUntil(): DateTimeInterface
    {
        return now()->addMinutes(15);
    }

    public function handle(GenerateAutoPayScheduleAction $auto_pay_schedule_action): void
    {
        $schedule = AuditSubMdaSchedule::find($this->scheduleId);

        // Under a burst dispatch the row can be momentarily invisible to this
        // worker; release and retry rather than failing. A row that is truly
        // gone still surfaces once retryUntil() lapses.
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

    /**
     * A terminal failure (timeout, exhausted retries, or thrown exception) would
     * otherwise leave the parent category stuck on the 'running' status its
     * controller set at dispatch. Mark it 'failed' so the UI reflects that
     * generation errored, and record the reason for triage.
     */
    public function failed(?Throwable $exception): void
    {
        $category = AuditSubMdaSchedule::find($this->scheduleId)?->payrollCategory();

        if ($category !== null && $category->autopay_status === 'running') {
            $category->setAutopayStatus('failed');
        }

        Log::error('Autopay schedule generation failed.', [
            'schedule_id' => $this->scheduleId,
            'category_id' => $category?->id,
            'reason' => $exception?->getMessage(),
        ]);
    }
}
