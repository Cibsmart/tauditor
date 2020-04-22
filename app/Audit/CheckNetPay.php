<?php


namespace App\Audit;


use App\AuditPaySchedule;
use App\Contracts\Auditable;
use App\Classes\AuditCheckable;

class CheckNetPay extends AuditCheckable implements Auditable
{

    public function check(AuditPaySchedule $schedule)
    {
        $this->initialize($schedule);

        if($this->hasNoPreviousSchedule()){
            return;
        }

        $current_net_pay = $this->schedule->net_pay;

        $previous_net_pay = $this->last_schedule->net_pay;

        if($current_net_pay == $previous_net_pay){
            return;
        }

        $message = "NET PAY AMOUNT CHANGED FROM '$previous_net_pay' IN $this->last_payment TO '$current_net_pay' IN $this->this_month";

        $this->report(self::NET_PAY_CHANGED, $message, $current_net_pay, $previous_net_pay);

        return;
    }
}
