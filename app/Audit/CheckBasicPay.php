<?php


namespace App\Audit;


use App\AuditPaySchedule;
use App\Contracts\Auditable;
use App\Classes\AuditCheckable;

class CheckBasicPay extends AuditCheckable implements Auditable
{

    public function check(AuditPaySchedule $schedule)
    {
        $this->initialize($schedule);

        if($this->hasNoPreviousSchedule()){
            return;
        }

        $current_basic_pay = $this->schedule->basic_pay;

        $previous_basic_pay = $this->last_schedule->basic_pay;

        if($current_basic_pay == $previous_basic_pay){
            return;
        }

        $message = "BASIC PAY AMOUNT CHANGED FROM '$previous_basic_pay' IN $this->last_payment TO '$current_basic_pay' IN $this->this_month";

        $this->report(self::BASIC_PAY_CHANGED, $message, $current_basic_pay, $previous_basic_pay);

        return;
    }
}
