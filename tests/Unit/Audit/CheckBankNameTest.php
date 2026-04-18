<?php

use App\Audit\CheckBankName;
use App\Models\AuditPaySchedule;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class SpyCheckBankName extends CheckBankName
{
    public array $reports = [];

    private Collection $stubbedPreviousSchedules;

    public function __construct(Collection $previousSchedules)
    {
        $this->stubbedPreviousSchedules = $previousSchedules;
    }

    protected function initialize(AuditPaySchedule $schedule)
    {
        $this->schedule = $schedule;
        $this->month = $schedule->month;
        $this->previous_schedules = $this->stubbedPreviousSchedules;
        $this->last_schedule = $this->previous_schedules->first();
        $this->this_month = "{$schedule->month->monthName} {$schedule->month->year}";

        if ($this->last_schedule) {
            $this->last_payment = "{$this->last_schedule->month->monthName} {$this->last_schedule->month->year}";
        }
    }

    protected function report($category, $message, $current = null, $previous = null)
    {
        $this->reports[] = compact('category', 'message', 'current', 'previous');
    }
}

function makeSchedule(string $bankName, string $month): AuditPaySchedule
{
    $schedule = new class extends AuditPaySchedule {
        protected $casts = [];
    };
    $schedule->bank_name = $bankName;
    $schedule->month = Carbon::parse($month);

    return $schedule;
}

it('does nothing when there is no previous schedule', function () {
    $schedule = makeSchedule('First Bank', '2025-03-15');

    $check = new SpyCheckBankName(new Collection());
    $check->check($schedule);

    expect($check->reports)->toBe([]);
});

it('does nothing when bank name is unchanged', function () {
    $schedule = makeSchedule('First Bank', '2025-03-15');
    $previous = makeSchedule('First Bank', '2025-02-15');

    $check = new SpyCheckBankName(new Collection([$previous]));
    $check->check($schedule);

    expect($check->reports)->toBe([]);
});

it('reports when bank name has changed', function () {
    $schedule = makeSchedule('GTBank', '2025-03-15');
    $previous = makeSchedule('First Bank', '2025-02-15');

    $check = new SpyCheckBankName(new Collection([$previous]));
    $check->check($schedule);

    expect($check->reports)->toHaveCount(1);
    expect($check->reports[0]['category'])->toBe('changed_bank');
    expect($check->reports[0]['current'])->toBe('GTBank');
    expect($check->reports[0]['previous'])->toBe('First Bank');
    expect($check->reports[0]['message'])->toBe(
        "BANK CHANGED FROM 'First Bank' IN February 2025 TO 'GTBank' IN March 2025"
    );
});

it('uses the most recent previous schedule for comparison', function () {
    $schedule = makeSchedule('GTBank', '2025-03-15');
    $mostRecent = makeSchedule('Access Bank', '2025-02-15');
    $older = makeSchedule('First Bank', '2025-01-15');

    $check = new SpyCheckBankName(new Collection([$mostRecent, $older]));
    $check->check($schedule);

    expect($check->reports)->toHaveCount(1);
    expect($check->reports[0]['previous'])->toBe('Access Bank');
    expect($check->reports[0]['current'])->toBe('GTBank');
});
