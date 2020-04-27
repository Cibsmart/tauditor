<?php


namespace App\Audit;

use App\AuditPaySchedule;
use App\Contracts\Auditable;
use App\Classes\AuditCheckable;

class CheckAccountNumber extends AuditCheckable implements Auditable
{
    public function check(AuditPaySchedule $schedule)
    {
        $this->initialize($schedule);

        if ($this->hasNoPreviousSchedule()) {
            return;
        }

        $current_account_number = $this->schedule->account_number;

        $previous_account_number = $this->last_schedule->account_number;

        if ($current_account_number == $previous_account_number) {
            return;
        }

        $message = sprintf(
            "ACCOUNT NUMBER CHANGED FROM '%s' IN %s TO '%s' IN %s",
            $previous_account_number,
            $this->last_payment,
            $current_account_number,
            $this->this_month
        );

        $this->report(self::ACCOUNT_NUMBER_CHANGED, $message, $current_account_number, $previous_account_number);

        return;
    }
}
