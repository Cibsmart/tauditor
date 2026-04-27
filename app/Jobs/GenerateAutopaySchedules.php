<?php

namespace App\Jobs;

use App\Actions\GenerateAutoPayScheduleAction;
use App\Models\AuditSubMdaSchedule;
use App\Models\Domain;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class GenerateAutopaySchedules implements ShouldQueue
{
    use Queueable;

    public int $timeout = 0;

    public function __construct(public Domain $domain, public AuditSubMdaSchedule $schedule) {}

    public function handle(GenerateAutoPayScheduleAction $auto_pay_schedule_action)
    {
        $auto_pay_schedule_action->execute($this->domain, $this->schedule);
    }
}
