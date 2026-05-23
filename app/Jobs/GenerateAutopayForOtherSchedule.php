<?php

namespace App\Jobs;

use App\Actions\GenerateAutopayOtherScheduleAction;
use App\Models\OtherAuditPayrollCategory;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class GenerateAutopayForOtherSchedule implements ShouldQueue
{
    use Queueable;

    public int $tries = 5;

    public function __construct(public int $categoryId) {}

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
