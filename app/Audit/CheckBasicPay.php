<?php


namespace App\Audit;

use App\AuditPaySchedule;
use App\Classes\AuditCheckable;

class CheckBasicPay extends AuditCheckable
{
    public function check(AuditPaySchedule $schedule)
    {
        $this->initialize($schedule);

        if ($this->hasNoPreviousSchedule()) {
            return;
        }

        $current_basic_pay = $this->schedule->basic_pay;

        $previous_basic_pay = $this->last_schedule->basic_pay;

        if ($current_basic_pay == $previous_basic_pay) {
            return;
        }

        $message = sprintf(
            "BASIC PAY AMOUNT CHANGED FROM '%s' IN %s TO '%s' IN %s",
            $previous_basic_pay,
            $this->last_payment,
            $current_basic_pay,
            $this->this_month
        );

        $this->report(self::BASIC_PAY_CHANGED, $message, $current_basic_pay, $previous_basic_pay);

        return;
    }
}
