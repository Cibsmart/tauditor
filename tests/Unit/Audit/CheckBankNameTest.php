<?php

namespace Tests\Unit\Audit;

use App\Audit\CheckBankName;
use App\Models\AuditPaySchedule;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;

class CheckBankNameTest extends TestCase
{
    /** @test */
    public function it_does_nothing_when_there_is_no_previous_schedule()
    {
        $schedule = $this->makeSchedule('First Bank', '2025-03-15');

        $check = new SpyCheckBankName(new Collection());
        $check->check($schedule);

        $this->assertSame([], $check->reports);
    }

    /** @test */
    public function it_does_nothing_when_bank_name_is_unchanged()
    {
        $schedule = $this->makeSchedule('First Bank', '2025-03-15');
        $previous = $this->makeSchedule('First Bank', '2025-02-15');

        $check = new SpyCheckBankName(new Collection([$previous]));
        $check->check($schedule);

        $this->assertSame([], $check->reports);
    }

    /** @test */
    public function it_reports_when_bank_name_has_changed()
    {
        $schedule = $this->makeSchedule('GTBank', '2025-03-15');
        $previous = $this->makeSchedule('First Bank', '2025-02-15');

        $check = new SpyCheckBankName(new Collection([$previous]));
        $check->check($schedule);

        $this->assertCount(1, $check->reports);
        $this->assertSame('changed_bank', $check->reports[0]['category']);
        $this->assertSame('GTBank', $check->reports[0]['current']);
        $this->assertSame('First Bank', $check->reports[0]['previous']);
        $this->assertSame(
            "BANK CHANGED FROM 'First Bank' IN February 2025 TO 'GTBank' IN March 2025",
            $check->reports[0]['message']
        );
    }

    /** @test */
    public function it_uses_the_most_recent_previous_schedule_for_comparison()
    {
        $schedule = $this->makeSchedule('GTBank', '2025-03-15');
        $mostRecent = $this->makeSchedule('Access Bank', '2025-02-15');
        $older = $this->makeSchedule('First Bank', '2025-01-15');

        $check = new SpyCheckBankName(new Collection([$mostRecent, $older]));
        $check->check($schedule);

        $this->assertCount(1, $check->reports);
        $this->assertSame('Access Bank', $check->reports[0]['previous']);
        $this->assertSame('GTBank', $check->reports[0]['current']);
    }

    private function makeSchedule(string $bankName, string $month): AuditPaySchedule
    {
        $schedule = new class extends AuditPaySchedule {
            protected $casts = [];
        };
        $schedule->bank_name = $bankName;
        $schedule->month = Carbon::parse($month);

        return $schedule;
    }
}

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
