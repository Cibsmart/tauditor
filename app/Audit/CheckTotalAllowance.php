<?php


namespace App\Audit;

use App\AuditPaySchedule;
use App\Classes\AuditCheckable;

class CheckTotalAllowance extends AuditCheckable
{
    public function check(AuditPaySchedule $schedule)
    {
        $this->initialize($schedule);

        if ($this->hasNoPreviousSchedule()) {
            return;
        }

        $current_total_allowance = $this->schedule->total_allowance;

        $previous_total_allowance = $this->last_schedule->total_allowance;

        if ($current_total_allowance == $previous_total_allowance) {
            return;
        }

        $message = sprintf(
            "TOTAL ALLOWANCE AMOUNT CHANGED FROM '%s' IN %s TO '%s' IN %s",
            $previous_total_allowance,
            $this->last_payment,
            $current_total_allowance,
            $this->this_month
        );

        $this->report(self::TOTAL_ALLOWANCE_CHANGED, $message, $current_total_allowance, $previous_total_allowance);

        return;
    }
}
