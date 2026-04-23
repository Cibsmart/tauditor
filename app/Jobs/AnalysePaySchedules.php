<?php

namespace App\Jobs;

use App\Actions\AuditPayScheduleAction;
use App\Models\AuditSubMdaSchedule;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AnalysePaySchedules implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public AuditSubMdaSchedule $schedule;

    /**
     * Create a new job instance.
     */
    public function __construct(AuditSubMdaSchedule $schedule)
    {
        $this->schedule = $schedule;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(AuditPayScheduleAction $schedule_action)
    {
        $schedule_action->execute($this->schedule);
    }
}
