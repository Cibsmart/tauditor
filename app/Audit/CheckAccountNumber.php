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


        $current_account_number = $this->schedule->account_number;

        $last_schedule = $this->previous_schedules->first();

        if($this->hasNoPreviousSchedule()){
            return;
        }

        $previous_account_number = $last_schedule->account_number;

        if($current_account_number == $previous_account_number){
            return;
        }

        $this_month = $this->getMonthYear($this->schedule->month);
        $last_payment = $this->getMonthYear($last_schedule->month);

        $content = "ACCOUNT NUMBER CHANGED FROM $previous_account_number IN $last_payment TO $current_account_number IN $this_month";

        $this->report(self::ACCOUNT_NUMBER_CHANGED, $content);

        return;
    }
}
