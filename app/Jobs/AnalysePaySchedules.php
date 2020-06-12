<?php

namespace App\Jobs;

use App\AuditSubMdaSchedule;
use Illuminate\Bus\Queueable;
use App\Actions\AuditPayScheduleAction;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AnalysePaySchedules implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var AuditSubMdaSchedule
     */
    public AuditSubMdaSchedule $schedule;

    /**
     * Create a new job instance.
     *
     * @param  AuditSubMdaSchedule  $schedule
     */
    public function __construct(AuditSubMdaSchedule $schedule)
    {
        $this->schedule = $schedule;
    }

    /**
     * Execute the job.
     *
     * @param  AuditPayScheduleAction  $schedule_action
     * @return void
     */
    public function handle(AuditPayScheduleAction $schedule_action)
    {
        $schedule_action->execute($this->schedule);
    }
}
