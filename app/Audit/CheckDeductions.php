<?php

namespace App\Audit;

use App\Classes\AuditCheckable;
use App\Models\AuditPaySchedule;
use Illuminate\Support\Collection;

use function collect;

class CheckDeductions extends AuditCheckable
{
    protected Collection $current_deductions;

    protected Collection $previous_deductions;

    public function check(AuditPaySchedule $schedule)
    {
        $this->initialize($schedule);

        if ($this->hasNoPreviousSchedule()) {
            return;
        }

        $this->current_deductions = collect($this->schedule->deductions);

        $this->previous_deductions = collect($this->last_schedule->deductions);

        if ($this->current_deductions === $this->previous_deductions) {
            return;
        }

        $this->checkAndReportNewDeductions();

        $this->checkAndReportRemovedDeductions();

        $this->checkAndReportChangedDeductions();

    }

    private function checkAndReportNewDeductions()
    {
        $current_diffs = $this->current_deductions->diffKeys($this->previous_deductions);

        if ($current_diffs->isEmpty()) {
            return;
        }

        $message = 'DEDUCTIONS(S) ADDED';

        $this->report(self::DEDUCTION_ADDED, $message, $current_diffs, null);
    }

    private function checkAndReportRemovedDeductions()
    {
        $previous_diffs = $this->previous_deductions->diffKeys($this->current_deductions);

        if ($previous_diffs->isEmpty()) {
            return;
        }

        $message = 'DEDUCTIONS(S) REMOVED';

        $this->report(self::DEDUCTION_REMOVED, $message, null, $previous_diffs);
    }

    private function checkAndReportChangedDeductions()
    {
        $new_deductions = $this->current_deductions->diffKeys($this->previous_deductions)->keys();

        $current_diffs = $this->current_deductions->diff($this->previous_deductions)->except($new_deductions);

        if ($current_diffs->isEmpty()) {
            return;
        }

        $removed_deductions = $this->previous_deductions->diffKeys($this->current_deductions)->keys();

        $previous_diffs = $this->previous_deductions->diff($this->current_deductions)->except($removed_deductions);

        $message = 'DEDUCTIONS(S) CHANGED IN VALUE';

        $this->report(self::DEDUCTION_CHANGED, $message, $current_diffs, $previous_diffs);
    }
}
