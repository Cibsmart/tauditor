<?php


namespace App\Audit;


use App\AuditPaySchedule;
use App\Classes\AuditCheckable;
use Illuminate\Support\Str;
use App\Contracts\Auditable;
use function is_null;

class CheckNewBeneficiary extends AuditCheckable implements Auditable
{
    public function check(AuditPaySchedule $schedule)
    {
        $this->initialize($schedule);

        if($this->hasNoPreviousSchedule()){
            $this->report(self::NEW_BENEFICIARY, 'NEW BENEFICIARY');
            return;
        }

        if($this->wasPaidLastMonth()){
            return;
        }

        $content = $this->getContentForRestoredBeneficiary();

        $this->report(self::RESTORED_BENEFICIARY, $content);

        return;
    }

    private function wasPaidLastMonth()
    {
        $last_month_schedule = $this->previous_schedules->where('month', '=', $this->month->subMonth())->first();

        return ! is_null($last_month_schedule);
    }

    private function getContentForRestoredBeneficiary()
    {
        $last_month = $this->getMonthYear($this->schedule->month->subMonth());

        return "NOT PAID IN $last_month and LAST PAYMENT WAS IN $this->last_payment. THEN APPEARED IN $this->this_month PAYMENT SCHEDULE";
    }
}
