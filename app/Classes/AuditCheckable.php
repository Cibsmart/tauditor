<?php


namespace App\Classes;


use Carbon\Carbon;
use App\AuditPaySchedule;
use Illuminate\Support\Str;

abstract class AuditCheckable
{
    protected $month;
    protected $domain;
    protected $payroll;

    protected string $category = '';
    protected string $content = '';

    protected $this_month;
    protected $last_payment;

    protected $schedule;
    protected $last_schedule;
    protected $previous_schedules;

    protected const BANK_CHANGED = 'changed_bank';
    protected const NET_PAY_CHANGED = 'changed_net_pay';
    protected const NEW_BENEFICIARY = 'new_beneficiary';
    protected const BASIC_PAY_CHANGED = 'changed_basic_pay';
    protected const GROSS_PAY_CHANGED = 'changed_gross_pay';
    protected const RESTORED_BENEFICIARY = 'restored_beneficiary';
    protected const ACCOUNT_NUMBER_CHANGED = 'changed_account_number';

    protected function initialize(AuditPaySchedule $schedule)
    {
        $this->schedule = $schedule;

        $this->month = $this->schedule->month;

        $this->payroll = $this->schedule->auditPayroll();

        $this->previous_schedules = $this->previousSchedules();

        $this->last_schedule = $this->previous_schedules->first();

        $this->this_month = $this->getMonthYear($this->schedule->month);

        if($this->last_schedule) {
            $this->last_payment = $this->getMonthYear($this->last_schedule->month);
        }
    }

    protected function report($category, $content)
    {
        $this->setCategory($category)
             ->setContent($content)
             ->thenReport();
    }

    protected function thenReport()
    {
        $this->schedule->report($this->payroll->id, $this->category, $this->content);
    }

    protected function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    protected function setContent($content)
    {
        $this->content = Str::upper($content);

        return $this;
    }

    protected function previousSchedules()
    {
        return AuditPaySchedule::where('verification_number', $this->schedule->verification_number)
                                ->where('month', '<', $this->month)
                                ->latest('month')
                                ->take(12)
                                ->get();
    }


    protected function hasNoPreviousSchedule()
    {
        return $this->previous_schedules->isEmpty();
    }

    protected function getMonthYear(Carbon $carbon)
    {
        return "$carbon->monthName $carbon->year";
    }
}
