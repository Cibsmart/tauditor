<?php

namespace Tests\Unit\Audit;

use App\Audit\Analyse;
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
use Illuminate\Container\Container;
use Mockery;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class AnalyseTest extends TestCase
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
    public function it_runs_every_registered_check_against_the_schedule()
    {
        $schedule = new AuditPaySchedule();

        $expected = [
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

        $this->assertSame($expected, $this->registeredChecks(), 'Analyse::$checks list drifted — update the test or the Analyse class.');

        foreach ($expected as $class) {
            $mock = Mockery::mock($class);
            $mock->shouldReceive('check')->once()->with($schedule);
            Container::getInstance()->instance($class, $mock);
        }

        (new Analyse())->check($schedule);

        $this->addToAssertionCount(count($expected));
    }

    private function registeredChecks(): array
    {
        $property = (new ReflectionClass(Analyse::class))->getProperty('checks');

        return $property->getValue(new Analyse());
    }
}
