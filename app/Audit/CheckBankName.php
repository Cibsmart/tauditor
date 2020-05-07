<?php

namespace App\Audit;

use App\AuditPaySchedule;
use App\Classes\AuditCheckable;

class CheckBankName extends AuditCheckable
{
    public function check(AuditPaySchedule $schedule)
    {
        $this->initialize($schedule);

        if ($this->hasNoPreviousSchedule()) {
            return;
        }

        $current_bank_name = $this->schedule->bank_name;

        $previous_bank_name = $this->last_schedule->bank_name;

        if ($current_bank_name == $previous_bank_name) {
            return;
        }

        $message = sprintf(
            "BANK CHANGED FROM '%s' IN %s TO '%s' IN %s",
            $previous_bank_name,
            $this->last_payment,
            $current_bank_name,
            $this->this_month
        );

        $this->report(self::BANK_CHANGED, $message, $current_bank_name, $previous_bank_name);

        return;
    }
}
