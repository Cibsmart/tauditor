<?php

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

function allCheckClasses(): array
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

beforeEach(function () {
    Container::setInstance(new Container());
});

afterEach(function () {
    Mockery::close();
    Container::setInstance(null);
});

it('runs every check on every schedule', function () {
    $schedule1 = new AuditPaySchedule();
    $schedule2 = new AuditPaySchedule();
    $schedules = new Collection([$schedule1, $schedule2]);

    foreach (allCheckClasses() as $class) {
        $mock = Mockery::mock($class);
        $mock->shouldReceive('check')->twice()->with(Mockery::type(AuditPaySchedule::class));
        Container::getInstance()->instance($class, $mock);
    }

    $subMda = new SpySubMda($schedules);

    (new AuditPayScheduleAction())->execute($subMda);

    expect($subMda->analysisCalled)->toBeTrue('analysisCompleted() was not called');
});

it('marks analysis completed even with no schedules', function () {
    foreach (allCheckClasses() as $class) {
        Container::getInstance()->instance($class, Mockery::mock($class));
    }

    $subMda = new SpySubMda(new Collection());

    (new AuditPayScheduleAction())->execute($subMda);

    expect($subMda->analysisCalled)->toBeTrue('analysisCompleted() was not called for an empty schedule list');
});
