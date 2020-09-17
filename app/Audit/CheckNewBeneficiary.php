<?php


namespace App\Audit;

use App\Models\AuditPaySchedule;
use App\Classes\AuditCheckable;
use function is_null;

class CheckNewBeneficiary extends AuditCheckable
{
    public function check(AuditPaySchedule $schedule)
    {
        $this->initialize($schedule);

        if ($this->hasNoPreviousSchedule()) {
            $this->report(self::NEW_BENEFICIARY, 'NEW BENEFICIARY');
            return;
        }

        if ($this->wasPaidLastMonth()) {
            return;
        }

        $message = $this->getContentForRestoredBeneficiary();

        $this->report(self::RESTORED_BENEFICIARY, $message);

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

        return sprintf(
            "NOT PAID IN %s and LAST PAYMENT WAS IN %s. THEN APPEARED IN %s PAYMENT SCHEDULE",
            $last_month,
            $this->last_payment,
            $this->this_month
        );
    }
}
