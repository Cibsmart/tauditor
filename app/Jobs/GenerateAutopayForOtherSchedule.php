<?php

namespace App\Jobs;

use App\Actions\GenerateAutopayOtherScheduleAction;
use App\Models\OtherAuditPayrollCategory;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateAutopayForOtherSchedule implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public OtherAuditPayrollCategory $category;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(OtherAuditPayrollCategory $category)
    {
        $this->category = $category;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(GenerateAutopayOtherScheduleAction $action)
    {
        $action->execute($this->category);
    }
}
