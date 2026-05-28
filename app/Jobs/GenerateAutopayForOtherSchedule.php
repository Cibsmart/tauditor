<?php

namespace App\Jobs;

use App\Actions\GenerateAutopayOtherScheduleAction;
use App\Models\OtherAuditPayrollCategory;
use DateTimeInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class GenerateAutopayForOtherSchedule implements ShouldQueue
{
    use Queueable;

    public int $timeout = 180;

    public function __construct(public int $categoryId) {}

    // Bound retries on wall-clock instead of attempt count so a worker
    // restart or release-on-missing-row doesn't burn the budget; must
    // be < redis retry_after (210s) to avoid concurrent re-issue.
    public function retryUntil(): DateTimeInterface
    {
        return now()->addMinutes(15);
    }

    public function handle(GenerateAutopayOtherScheduleAction $action): void
    {
        $category = OtherAuditPayrollCategory::find($this->categoryId);

        // Under a burst dispatch the row can be momentarily invisible to this
        // worker; release and retry rather than failing. A row that is truly
        // gone still surfaces once $tries is exhausted.
        if ($category === null) {
            $this->release(5);

            return;
        }

        // A retry may land after a prior attempt already generated the autopay;
        // skip to avoid creating duplicate schedule rows.
        if ($category->autopay_generated !== null) {
            return;
        }

        $action->execute($category);
    }
}
