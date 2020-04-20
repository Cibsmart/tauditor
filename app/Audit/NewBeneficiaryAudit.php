<?php


namespace App\Audit;


use App\AuditPaySchedule;
use App\Classes\AuditCheckable;
use Illuminate\Support\Str;
use App\Contracts\Auditable;

class NewBeneficiaryAudit extends AuditCheckable implements Auditable
{
    private $schedule;
    private $previous_schedules;

    public function check(AuditPaySchedule $schedule)
    {
        $this->schedule = $schedule;

        $this->month = $this->schedule->month;

        $this->payroll = $this->schedule->auditPayroll();

        if($this->hasNoPreviousSchedule()){
            $this->report(self::NEW_BENEFICIARY, 'NEW BENEFICIARY');
            return;
        }

        if($this->wasPaidLastMonth()){
            return;
        }

        $content = $this->getRestoredContent();

        $this->report(self::RESTORED_BENEFICIARY, $content);

        return;
    }

    private function hasNoPreviousSchedule()
    {
        $this->previous_schedules = AuditPaySchedule::where('verification_number', $this->schedule->verification_number)
                                                    ->where('month', '<', $this->month)
                                                    ->get();
        return $this->previous_schedules->isEmpty();
    }

    private function wasPaidLastMonth()
    {
        $last_month_schedule = AuditPaySchedule::where('verification_number', $this->schedule->verification_number)
                                               ->where('month', '=', $this->month->subMonth())
                                                ->get();
        return $last_month_schedule->isNotEmpty();
    }

    private function report($category, $content)
    {
        $this->item($this->schedule)
             ->isInCategory($category)
             ->setContent($content)
             ->thenReport();
    }

    private function getRestoredContent()
    {
        $last_schedule = $this->previous_schedules->sortByDesc('month')->first();

        $this_month_timestamp = $this->schedule->month;
        $last_month_timestamp = $this->schedule->month->subMonth();
        $last_payment_timestamp = $last_schedule->month;

        $last_month = "$last_month_timestamp->monthName $last_month_timestamp->year";
        $this_month = "$this_month_timestamp->monthName $this_month_timestamp->year";
        $last_payment = "$last_payment_timestamp->monthName $last_payment_timestamp->year";

        return Str::upper("NOT PAID IN $last_month and LAST PAYMENT WAS IN $last_payment. THEN APPEARED IN $this_month PAYMENT SCHEDULE");
    }
}
