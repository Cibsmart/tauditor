<?php


namespace App\Audit;

use App\AuditPaySchedule;
use App\Contracts\Auditable;
use App\Classes\AuditCheckable;
use Illuminate\Support\Collection;

class CheckAllowances extends AuditCheckable implements Auditable
{
    protected Collection $current_allowances;
    protected Collection $previous_allowances;

    public function check(AuditPaySchedule $schedule)
    {
        $this->initialize($schedule);

        if ($this->hasNoPreviousSchedule()) {
            return;
        }

        $this->current_allowances = collect($this->schedule->allowances);

        $this->previous_allowances = collect($this->last_schedule->allowances);

        if ($this->current_allowances === $this->previous_allowances) {
            return;
        }

        $this->checkForNewAllowances();

        $this->checkForRemovedAllowances();

        $this->checkChangedAllowances();

        return;
    }

    private function checkForNewAllowances()
    {
        $current_diffs = $this->current_allowances->diffKeys($this->previous_allowances);

        if ($current_diffs->isEmpty()) {
            return;
        }

        $message = "ALLOWANCE(S) ADDED";

        $this->report(self::ALLOWANCES_ADDED, $message, $current_diffs, null);
    }

    private function checkForRemovedAllowances()
    {
        $previous_diffs = $this->previous_allowances->diffKeys($this->current_allowances);

        if ($previous_diffs->isEmpty()) {
            return;
        }

        $message = "ALLOWANCE(S) REMOVED";

        $this->report(self::ALLOWANCES_REMOVED, $message, null, $previous_diffs);
    }

    private function checkChangedAllowances()
    {
        $new_allowances = $this->current_allowances->diffKeys($this->previous_allowances)->keys();

        $current_diffs = $this->current_allowances->diff($this->previous_allowances)->except($new_allowances);

        if ($current_diffs->isEmpty()) {
            return;
        }

        $removed_allowances = $this->previous_allowances->diffKeys($this->current_allowances)->keys();

        $previous_diffs = $this->previous_allowances->diff($this->current_allowances)->except($removed_allowances);

        $message = "ALLOWANCE(S) CHANGED IN VALUE ";

        $this->report(self::ALLOWANCES_CHANGED, $message, $current_diffs, $previous_diffs);
    }
}
