<?php


namespace App\Audit;


use App\AuditPaySchedule;
use App\Contracts\Auditable;
use App\Classes\AuditCheckable;


class CheckBankName extends AuditCheckable implements Auditable
{
    public function check(AuditPaySchedule $schedule)
    {
        $this->initialize($schedule);

        if($this->hasNoPreviousSchedule()){
            return;
        }

        $current_bank_name = $this->schedule->bank_name;

        $previous_bank_name = $this->last_schedule->bank_name;

        if($current_bank_name == $previous_bank_name){
            return;
        }

        $content = "BANK CHANGED FROM '$previous_bank_name' IN $this->last_payment TO '$current_bank_name' IN $this->this_month";

        $this->report(self::BANK_CHANGED, $content);

        return;
    }
}
