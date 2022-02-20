<?php

namespace App\Audit;

use App\Classes\AuditCheckable;
use App\Models\AuditPaySchedule;

class CheckTotalDeduction extends AuditCheckable
{
    public function check(AuditPaySchedule $schedule)
    {
        $this->initialize($schedule);

        if ($this->hasNoPreviousSchedule()) {
            return;
        }

        $current_total_deduction = $this->schedule->total_deduction;

        $previous_total_deduction = $this->last_schedule->total_deduction;

        if ($current_total_deduction == $previous_total_deduction) {
            return;
        }

        $message = sprintf(
            "TOTAL DEDUCTION AMOUNT CHANGED FROM '%s' IN %s TO '%s' IN %s",
            $previous_total_deduction,
            $this->last_payment,
            $current_total_deduction,
            $this->this_month
        );

        $this->report(self::TOTAL_DEDUCTION_CHANGED, $message, $current_total_deduction, $previous_total_deduction);

    }
}
