<?php


namespace App\Audit;


use App\AuditPaySchedule;
use App\Contracts\Auditable;
use App\Classes\AuditCheckable;

class CheckGrossPay extends AuditCheckable implements Auditable
{

    public function check(AuditPaySchedule $schedule)
    {
        $this->initialize($schedule);

        if($this->hasNoPreviousSchedule()){
            return;
        }

        $current_gross_pay = $this->schedule->gross_pay;

        $previous_gross_pay = $this->last_schedule->gross_pay;

        if($current_gross_pay == $previous_gross_pay){
            return;
        }

        $message = "GROSS PAY AMOUNT CHANGED FROM '$previous_gross_pay' IN $this->last_payment TO '$current_gross_pay' IN $this->this_month";

        $this->report(self::GROSS_PAY_CHANGED, $message, $current_gross_pay, $previous_gross_pay);

        return;
    }
}
