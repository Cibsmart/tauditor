<?php


namespace App\Classes;


abstract class AuditCheckable
{
    protected $month;
    protected $domain;
    protected $payroll;

    protected $reportable;
    protected string $category = '';
    protected string $content = '';

    protected const NEW_BENEFICIARY = 'new';
    protected const RESTORED_BENEFICIARY = 'restored';
    protected const ACCOUNT_NUMBER_CHANGED = 'account_number_changed';
    protected const BANK_CHANGED = 'bank_changed';

    protected function thenReport()
    {
        $this->reportable->report($this->payroll->id, $this->category, $this->content);
    }

    protected function item($reportable)
    {
        $this->reportable = $reportable;

        return $this;
    }

    protected function isInCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    protected function setContent($content)
    {
        $this->content = $content;

        return $this;
    }
}
