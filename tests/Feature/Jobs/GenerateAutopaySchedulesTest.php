<?php

use App\Actions\GenerateAutoPayScheduleAction;
use App\Jobs\GenerateAutopaySchedules;
use Illuminate\Contracts\Queue\Job as QueueJob;
use Tests\Feature\Actions\AutopayTestSetup;
use Tests\TestCase;

uses(AutopayTestSetup::class);

function makeSubMda(AutopayTestSetup|TestCase $test)
{
    $domain = $test->createDomain();
    $user = $test->createUser($domain);
    $paymentType = $test->createPaymentType();
    $payroll = $test->createAuditPayroll($domain, $user);
    $category = $test->createAuditPayrollCategory($payroll, $paymentType);
    $beneficiaryType = $test->createBeneficiaryType($domain);
    $mda = $test->createMda($beneficiaryType);
    $mdaSchedule = $test->createAuditMdaSchedule($category, $mda);

    return [$domain, $test->createAuditSubMdaSchedule($mdaSchedule)];
}

it('releases for a later attempt when the sub-MDA row is not yet visible', function () {
    $domain = $this->createDomain();

    $job = new GenerateAutopaySchedules($domain, 999999);

    $queueJob = Mockery::mock(QueueJob::class);
    $queueJob->shouldReceive('release')->once()->with(5);
    $job->setJob($queueJob);

    $action = Mockery::mock(GenerateAutoPayScheduleAction::class);
    $action->shouldNotReceive('execute');

    $job->handle($action);
});

it('generates autopay when the sub-MDA row exists and is not yet generated', function () {
    [$domain, $subMda] = makeSubMda($this);

    $job = new GenerateAutopaySchedules($domain, $subMda->id);

    $action = Mockery::mock(GenerateAutoPayScheduleAction::class);
    $action->shouldReceive('execute')->once();

    $job->handle($action);
});

it('skips generation when autopay was already produced by a prior attempt', function () {
    [$domain, $subMda] = makeSubMda($this);
    $subMda->autopay_generated = now();
    $subMda->save();

    $job = new GenerateAutopaySchedules($domain, $subMda->id);

    $action = Mockery::mock(GenerateAutoPayScheduleAction::class);
    $action->shouldNotReceive('execute');

    $job->handle($action);
});
