<?php

namespace App\Jobs;

use App\Actions\GenerateAutoPayScheduleAction;
use App\Models\AuditSubMdaSchedule;
use App\Models\Domain;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateAutopaySchedules implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public AuditSubMdaSchedule $schedule;

    public Domain $domain;

    /**
     * Create a new job instance.
     */
    public function __construct(Domain $domain, AuditSubMdaSchedule $schedule)
    {
        //
        $this->schedule = $schedule;
        $this->domain = $domain;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(GenerateAutoPayScheduleAction $auto_pay_schedule_action)
    {
        $auto_pay_schedule_action->execute($this->domain, $this->schedule);
    }
}
