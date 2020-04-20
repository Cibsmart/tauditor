<?php


namespace App\Audit;


use Carbon\Carbon;
use App\AuditPaySchedule;
use App\Contracts\Auditable;

class NewBeneficiaryAudit implements Auditable
{

    protected $month;
    protected $domain;

    public function check(AuditPaySchedule $schedule)
    {
        $this->month = $schedule->month;

        $this->domain = $schedule->domain();

        $previous_schedule = $this->hasPreviousSchedule($schedule);

        if($previous_schedule->isEmpty()){
            $this->report();
        }

        dd($previous_schedule);
    }

    public function report()
    {

    }

    private function hasPreviousSchedule(AuditPaySchedule $schedule)
    {
        return AuditPaySchedule::where('verification_number', $schedule->verification_number)
                               ->where('month', '<', $this->month)
                               ->get();
    }
}
