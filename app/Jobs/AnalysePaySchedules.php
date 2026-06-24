<?php

namespace App\Jobs;

use App\Actions\AuditPayScheduleAction;
use App\Models\AuditSubMdaSchedule;
use DateTimeInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Throwable;

class AnalysePaySchedules implements ShouldQueue
{
    use Queueable;

    public int $timeout = 180;

    public function __construct(public int $scheduleId) {}

    // Bound retries on wall-clock instead of attempt count so a worker
    // restart or release-on-missing-row doesn't burn the budget; must
    // be < redis retry_after (210s) to avoid concurrent re-issue.
    public function retryUntil(): DateTimeInterface
    {
        return now()->addMinutes(15);
    }

    public function handle(AuditPayScheduleAction $schedule_action): void
    {
        $schedule = AuditSubMdaSchedule::find($this->scheduleId);

        // Under a burst dispatch the row can be momentarily invisible to this
        // worker; release and retry rather than failing. A row that is truly
        // gone still surfaces once retryUntil() lapses.
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

    /**
     * A terminal failure (timeout, exhausted retries, or thrown exception) would
     * otherwise leave the parent category stuck on the 'running' status its
     * controller set at dispatch. Mark it 'failed' so the UI reflects that
     * analysis errored, and record the reason for triage.
     */
    public function failed(?Throwable $exception): void
    {
        $category = AuditSubMdaSchedule::find($this->scheduleId)?->payrollCategory();

        if ($category !== null && $category->analysis_status === 'running') {
            $category->setAnalysisStatus('failed');
        }

        Log::error('Pay schedule analysis failed.', [
            'schedule_id' => $this->scheduleId,
            'category_id' => $category?->id,
            'reason' => $exception?->getMessage(),
        ]);
    }
}
