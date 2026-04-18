<?php

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

beforeEach(function () {
    Container::setInstance(new Container());
});

afterEach(function () {
    Mockery::close();
    Container::setInstance(null);
});

it('runs every registered check against the schedule', function () {
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

    $property = (new ReflectionClass(Analyse::class))->getProperty('checks');
    $registeredChecks = $property->getValue(new Analyse());

    expect($registeredChecks)->toBe($expected, 'Analyse::$checks list drifted — update the test or the Analyse class.');

    foreach ($expected as $class) {
        $mock = Mockery::mock($class);
        $mock->shouldReceive('check')->once()->with($schedule);
        Container::getInstance()->instance($class, $mock);
    }

    (new Analyse())->check($schedule);
});
