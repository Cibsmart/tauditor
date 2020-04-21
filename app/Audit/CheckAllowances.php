<?php


namespace App\Audit;


use App\AuditPaySchedule;
use App\Contracts\Auditable;
use App\Classes\AuditCheckable;
use Illuminate\Support\Collection;
use function dd;
use function number_format;

class CheckAllowances extends AuditCheckable implements Auditable
{
    protected Collection $current_allowances;
    protected Collection $previous_allowances;

    public function check(AuditPaySchedule $schedule)
    {
        $this->initialize($schedule);

        if($this->hasNoPreviousSchedule()){
            return;
        }

        $this->current_allowances = collect($this->schedule->allowances);

        $this->previous_allowances = collect($this->last_schedule->allowances);

        if($this->current_allowances === $this->previous_allowances){
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

        if($current_diffs->isEmpty()){
            return;
        }

        $diffs = new Collection;

        foreach ($current_diffs as $key => $current_diff) {
            $amount = number_format($current_diff, 2);
            $diffs->push("$key:$amount");
        }

        $added_allowances = $diffs->join(', ', ', and ');

        $message = "THE FOLLOWING ALLOWANCE(S) WERE ADDED '$added_allowances'";

        $this->report(self::ALLOWANCES_ADDED, $message, $current_diffs, null);
    }

    private function checkForRemovedAllowances()
    {
        $previous_diffs = $this->previous_allowances->diffKeys($this->current_allowances);

        if($previous_diffs->isEmpty()){
            return;
        }

        $diffs = new Collection;

        foreach ($previous_diffs as $key => $previous_diff) {
            $amount = number_format($previous_diff, 2);
            $diffs->push("$key:$amount");
        }

        $removed_allowances = $diffs->join(', ', ', and ');

        $message = "THE FOLLOWING ALLOWANCE(S) WERE REMOVED '$removed_allowances'";

        $this->report(self::ALLOWANCES_REMOVED, $message, null, $previous_diffs);
    }

    private function checkChangedAllowances()
    {
        $new_allowances = $this->current_allowances->diffKeys($this->previous_allowances)->keys();

        $current_diffs = $this->current_allowances->diff($this->previous_allowances)->except($new_allowances);

        if($current_diffs->isEmpty()){
            return;
        }

        $curr_diffs = new Collection;
        $prev_diffs = new Collection;

        foreach ($current_diffs as $key => $current_diff) {
            $amount = number_format($current_diff, 2);
            $curr_diffs->push("$key:$amount");
        }

        $removed_allowances = $this->previous_allowances->diffKeys($this->current_allowances)->keys();

        $previous_diffs = $this->previous_allowances->diff($this->current_allowances)->except($removed_allowances);

        foreach ($previous_diffs as $key => $previous_diff) {
            $amount = number_format($previous_diff, 2);
            $prev_diffs->push("$key:$amount");
        }

        $original_allowances = $prev_diffs->join(', ', ', and ');
        $changed_allowances = $curr_diffs->join(', ', ', and ');

        $message = "THE FOLLOWING ALLOWANCE(S) CHANGED IN VALUE FROM '$original_allowances' TO '$changed_allowances'";

        $this->report(self::ALLOWANCES_CHANGED, $message, $current_diffs, $previous_diffs);
    }
}
