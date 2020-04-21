<?php


namespace App\Audit;


use App\AuditPaySchedule;
use App\Contracts\Auditable;
use App\Classes\AuditCheckable;

class CheckTotalAllowance extends AuditCheckable implements Auditable
{
    public function check(AuditPaySchedule $schedule)
    {
        $this->initialize($schedule);

        if($this->hasNoPreviousSchedule()){
            return;
        }

        $current_total_allowance = $this->schedule->total_allowance;

        $previous_total_allowance = $this->last_schedule->total_allowance;

        if($current_total_allowance == $previous_total_allowance){
            return;
        }

        $message = "TOTAL ALLOWANCE AMOUNT CHANGED FROM '$previous_total_allowance' IN $this->last_payment TO '$current_total_allowance' IN $this->this_month";

        $this->report(self::TOTAL_ALLOWANCE_CHANGED, $message, $current_total_allowance, $previous_total_allowance);

        return;
    }
}
