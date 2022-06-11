<?php

namespace App\Jobs;

use App\Actions\GenerateAutoPayScheduleAction;
use App\Models\AuditSubMdaSchedule;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateAutopaySchedules implements ShouldQueue
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
        //
        $this->schedule = $schedule;
    }

    /**
     * Execute the job.
     *
     * @param  GenerateAutoPayScheduleAction  $auto_pay_schedule_action
     * @return void
     */
    public function handle(GenerateAutoPayScheduleAction $auto_pay_schedule_action)
    {
        $auto_pay_schedule_action->execute($this->schedule);
    }
}
