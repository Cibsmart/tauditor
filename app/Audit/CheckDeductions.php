<?php


namespace App\Audit;


use App\AuditPaySchedule;
use App\Contracts\Auditable;
use App\Classes\AuditCheckable;
use Illuminate\Support\Collection;
use function collect;
use function number_format;

class CheckDeductions extends AuditCheckable implements Auditable
{
    protected Collection $current_deductions;
    protected Collection $previous_deductions;

    public function check(AuditPaySchedule $schedule)
    {
        $this->initialize($schedule);

        if($this->hasNoPreviousSchedule()){
            return;
        }

        $this->current_deductions = collect($this->schedule->deductions);

        $this->previous_deductions = collect($this->last_schedule->deductions);

        if($this->current_deductions === $this->previous_deductions){
            return;
        }

        $this->checkAndReportNewDeductions();

        $this->checkAndReportRemovedDeductions();

        $this->checkAndReportChangedDeductions();

        return;
    }

    private function checkAndReportNewDeductions()
    {
        $current_diffs = $this->current_deductions->diffKeys($this->previous_deductions);

        if($current_diffs->isEmpty()){
            return;
        }

        $diffs = new Collection;

        foreach ($current_diffs as $key => $current_diff) {
            $amount = number_format($current_diff, 2);
            $diffs->push("$key:$amount");
        }

        $added_deductions = $diffs->join(', ', ', and ');

        $message = "THE FOLLOWING ALLOWANCE(S) WERE ADDED '$added_deductions'";

        $this->report(self::DEDUCTION_ADDED, $message, $current_diffs, null);
    }

    private function checkAndReportRemovedDeductions()
    {
        $previous_diffs = $this->previous_deductions->diffKeys($this->current_deductions);

        if($previous_diffs->isEmpty()){
            return;
        }

        $diffs = new Collection;

        foreach ($previous_diffs as $key => $previous_diff) {
            $amount = number_format($previous_diff, 2);
            $diffs->push("$key:$amount");
        }

        $removed_deductions = $diffs->join(', ', ', and ');

        $message = "THE FOLLOWING ALLOWANCE(S) WERE REMOVED '$removed_deductions'";

        $this->report(self::DEDUCTION_REMOVED, $message, null, $previous_diffs);
    }

    private function checkAndReportChangedDeductions()
    {
        $new_deductions = $this->current_deductions->diffKeys($this->previous_deductions)->keys();

        $current_diffs = $this->current_deductions->diff($this->previous_deductions)->except($new_deductions);

        if($current_diffs->isEmpty()){
            return;
        }

        $curr_diffs = new Collection;
        $prev_diffs = new Collection;

        foreach ($current_diffs as $key => $current_diff) {
            $amount = number_format($current_diff, 2);
            $curr_diffs->push("$key:$amount");
        }

        $removed_deductions = $this->previous_deductions->diffKeys($this->current_deductions)->keys();

        $previous_diffs = $this->previous_deductions->diff($this->current_deductions)->except($removed_deductions);

        foreach ($previous_diffs as $key => $previous_diff) {
            $amount = number_format($previous_diff, 2);
            $prev_diffs->push("$key:$amount");
        }

        $original_deductions = $prev_diffs->join(', ', ', and ');
        $changed_deductions = $curr_diffs->join(', ', ', and ');

        $message = "THE FOLLOWING DEDUCTIONS(S) CHANGED IN VALUE FROM '$original_deductions' TO '$changed_deductions'";

        $this->report(self::DEDUCTION_CHANGED, $message, $current_diffs, $previous_diffs);
    }
}
