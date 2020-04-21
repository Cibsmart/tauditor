<?php


namespace App\Audit;


use App\AuditPaySchedule;
use App\Contracts\Auditable;
use App\Classes\AuditCheckable;

class CheckTotalDeduction extends AuditCheckable implements Auditable
{
    public function check(AuditPaySchedule $schedule)
    {
        $this->initialize($schedule);

        if($this->hasNoPreviousSchedule()){
            return;
        }

        $current_total_deduction = $this->schedule->total_deduction;

        $previous_total_deduction = $this->last_schedule->total_deduction;

        if($current_total_deduction == $previous_total_deduction){
            return;
        }

        $content = "TOTAL DEDUCTION AMOUNT CHANGED FROM '$previous_total_deduction' IN $this->last_payment TO '$current_total_deduction' IN $this->this_month";

        $this->report(self::TOTAL_DEDUCTION_CHANGED, $content);

        return;
    }
}
