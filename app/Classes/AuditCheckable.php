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

    protected $schedule;
    protected $previous_schedules;

    protected const NEW_BENEFICIARY = 'new';
    protected const RESTORED_BENEFICIARY = 'restored';
    protected const ACCOUNT_NUMBER_CHANGED = 'account_number_changed';
    protected const BANK_CHANGED = 'bank_changed';

    protected function initialize(AuditPaySchedule $schedule)
    {
        $this->schedule = $schedule;

        $this->month = $this->schedule->month;

        $this->payroll = $this->schedule->auditPayroll();

        $this->previous_schedules = $this->previousSchedules();
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
