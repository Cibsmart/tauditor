<?php


namespace App\Audit;

use App\AuditPaySchedule;
use App\Classes\AuditCheckable;

class CheckGrossPay extends AuditCheckable
{
    public function check(AuditPaySchedule $schedule)
    {
        $this->initialize($schedule);

        if ($this->hasNoPreviousSchedule()) {
            return;
        }

        $current_gross_pay = $this->schedule->gross_pay;

        $previous_gross_pay = $this->last_schedule->gross_pay;

        if ($current_gross_pay == $previous_gross_pay) {
            return;
        }

        $message = sprintf(
            "GROSS PAY AMOUNT CHANGED FROM '%s' IN %s TO '%s' IN %s",
            $previous_gross_pay,
            $this->last_payment,
            $current_gross_pay,
            $this->this_month
        );

        $this->report(self::GROSS_PAY_CHANGED, $message, $current_gross_pay, $previous_gross_pay);

        return;
    }
}
