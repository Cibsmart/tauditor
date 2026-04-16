<?php

namespace Tests\Unit\Actions;

use App\Actions\AuditPayScheduleAction;
use App\Audit\CheckAccountNumber;
use App\Audit\CheckAllowances;
use App\Audit\CheckBankName;
use App\Audit\CheckBasicPay;
use App\Audit\CheckDeductions;
use App\Audit\CheckGrossPay;
use App\Audit\CheckNetPay;
use App\Audit\CheckNewBeneficiary;
use App\Audit\CheckTotalAllowance;
use App\Audit\CheckTotalDeduction;
use App\Models\AuditPaySchedule;
use App\Models\AuditSubMdaSchedule;
use Illuminate\Container\Container;
use Illuminate\Support\Collection;
use Mockery;
use PHPUnit\Framework\TestCase;

class AuditPayScheduleActionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Container::setInstance(new Container());
    }

    protected function tearDown(): void
    {
        Mockery::close();
        Container::setInstance(null);
        parent::tearDown();
    }

    /** @test */
    public function it_runs_every_check_on_every_schedule()
    {
        $schedule1 = new AuditPaySchedule();
        $schedule2 = new AuditPaySchedule();
        $schedules  = new Collection([$schedule1, $schedule2]);

        foreach ($this->allCheckClasses() as $class) {
            $mock = Mockery::mock($class);
            $mock->shouldReceive('check')->twice()->with(Mockery::type(AuditPaySchedule::class));
            Container::getInstance()->instance($class, $mock);
        }

        $subMda = $this->stubSubMda($schedules);

        (new AuditPayScheduleAction())->execute($subMda);

        $this->assertTrue($subMda->analysisCalled, 'analysisCompleted() was not called');
        $this->addToAssertionCount(count($this->allCheckClasses()) * 2);
    }

    /** @test */
    public function it_marks_analysis_completed_even_with_no_schedules()
    {
        foreach ($this->allCheckClasses() as $class) {
            Container::getInstance()->instance($class, Mockery::mock($class));
        }

        $subMda = $this->stubSubMda(new Collection());

        (new AuditPayScheduleAction())->execute($subMda);

        $this->assertTrue($subMda->analysisCalled, 'analysisCompleted() was not called for an empty schedule list');
    }

    /**
     * Build an AuditSubMdaSchedule test double that returns the given collection
     * for $model->auditPaySchedules and tracks calls to analysisCompleted().
     */
    private function stubSubMda(Collection $schedules): SpySubMda
    {
        return new SpySubMda($schedules);
    }

    private function allCheckClasses(): array
    {
        return [
            CheckNewBeneficiary::class,
            CheckAccountNumber::class,
            CheckBankName::class,
            CheckBasicPay::class,
            CheckGrossPay::class,
            CheckNetPay::class,
            CheckTotalAllowance::class,
            CheckAllowances::class,
            CheckTotalDeduction::class,
            CheckDeductions::class,
        ];
    }
}

/**
 * Named test double so it can be referenced as a return type hint
 * and have public properties inspected after the call.
 */
class SpySubMda extends AuditSubMdaSchedule
{
    public bool $analysisCalled = false;

    private Collection $fakeSchedules;

    public function __construct(Collection $schedules)
    {
        $this->fakeSchedules = $schedules;
    }

    public function getAttribute($key)
    {
        if ($key === 'auditPaySchedules') {
            return $this->fakeSchedules;
        }
        return parent::getAttribute($key);
    }

    public function analysisCompleted(): void
    {
        $this->analysisCalled = true;
    }
}
