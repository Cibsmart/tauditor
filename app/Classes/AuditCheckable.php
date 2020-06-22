<?php


namespace App\Classes;

use Carbon\Carbon;
use App\AuditPaySchedule;
use Illuminate\Support\Str;

abstract class AuditCheckable
{
    protected $month;
    protected $domain;
    protected $payroll_category;
    protected $audit_sub_mda_schedule;

    protected string $category = '';
    protected string $message = '';
    protected $current_value = null;
    protected $previous_value = null;

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
    protected const TOTAL_ALLOWANCE_CHANGED = 'changed_total_allowance';
    protected const TOTAL_DEDUCTION_CHANGED = 'changed_total_deduction';
    protected const ALLOWANCES_ADDED = 'added_allowances';
    protected const DEDUCTION_ADDED = 'added_deductions';
    protected const ALLOWANCES_REMOVED = 'removed_allowances';
    protected const DEDUCTION_REMOVED = 'removed_deductions';
    protected const ALLOWANCES_CHANGED = 'changed_allowances';
    protected const DEDUCTION_CHANGED = 'changed_deductions';

    abstract public function check(AuditPaySchedule $schedule);

    protected function initialize(AuditPaySchedule $schedule)
    {
        $this->schedule = $schedule;

        $this->month = $schedule->month;

        $this->audit_sub_mda_schedule = $schedule->auditSubMdaSchedule;

        $this->payroll_category = $schedule->auditPayrollCategory();

        $this->previous_schedules = $this->previousSchedules();

        $this->last_schedule = $this->previous_schedules->first();

        $this->this_month = $this->getMonthYear($this->schedule->month);

        if ($this->last_schedule) {
            $this->last_payment = $this->getMonthYear($this->last_schedule->month);
        }
    }

    protected function report($category, $message, $current = null, $previous = null)
    {
        $this->setCategory($category)
             ->setMessage($message)
             ->setCurrent($current)
             ->setPrevious($previous)
             ->thenReport();
    }

    private function thenReport()
    {
        $this->schedule->report(
            $this->payroll_category->id,
            $this->audit_sub_mda_schedule->id,
            $this->category,
            $this->message,
            $this->current_value,
            $this->previous_value
        );
    }

    private function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    private function setMessage($message)
    {
        $this->message = Str::upper($message);

        return $this;
    }

    private function setPrevious($previous_value = null)
    {
        $this->previous_value = $previous_value;

        return $this;
    }

    private function setCurrent($current_value = null)
    {
        $this->current_value = $current_value;

        return $this;
    }

    private function previousSchedules()
    {
        return AuditPaySchedule::where('verification_number', $this->schedule->verification_number)
                                ->whereMonthLessThan($this->month)
                                ->orderByMonth()
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
