<?php

namespace App\Jobs;

use App\Actions\GenerateAutopayOtherScheduleAction;
use App\Models\OtherAuditPayrollCategory;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class GenerateAutopayForOtherSchedule implements ShouldQueue
{
    use Queueable;

    public function __construct(public OtherAuditPayrollCategory $category) {}

    public function handle(GenerateAutopayOtherScheduleAction $action)
    {
        $action->execute($this->category);
    }
}
