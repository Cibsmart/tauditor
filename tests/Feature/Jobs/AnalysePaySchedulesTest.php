<?php

use App\Actions\AuditPayScheduleAction;
use App\Jobs\AnalysePaySchedules;
use Illuminate\Contracts\Queue\Job as QueueJob;
use Tests\Feature\Actions\AutopayTestSetup;
use Tests\TestCase;

uses(AutopayTestSetup::class);

function makeSubMdaForAnalysis(AutopayTestSetup|TestCase $test)
{
    $domain = $test->createDomain();
    $user = $test->createUser($domain);
    $paymentType = $test->createPaymentType();
    $payroll = $test->createAuditPayroll($domain, $user);
    $category = $test->createAuditPayrollCategory($payroll, $paymentType);
    $beneficiaryType = $test->createBeneficiaryType($domain);
    $mda = $test->createMda($beneficiaryType);
    $mdaSchedule = $test->createAuditMdaSchedule($category, $mda);

    return $test->createAuditSubMdaSchedule($mdaSchedule);
}

it('releases for a later attempt when the sub-MDA row is not yet visible', function () {
    $job = new AnalysePaySchedules(999999);

    $queueJob = Mockery::mock(QueueJob::class);
    $queueJob->shouldReceive('release')->once()->with(5);
    $job->setJob($queueJob);

    $action = Mockery::mock(AuditPayScheduleAction::class);
    $action->shouldNotReceive('execute');

    $job->handle($action);
});

it('analyses when the sub-MDA row exists and is not yet analysed', function () {
    $subMda = makeSubMdaForAnalysis($this);

    $job = new AnalysePaySchedules($subMda->id);

    $action = Mockery::mock(AuditPayScheduleAction::class);
    $action->shouldReceive('execute')->once();

    $job->handle($action);
});

it('skips analysis when it was already completed by a prior attempt', function () {
    $subMda = makeSubMdaForAnalysis($this);
    $subMda->analysed = now();
    $subMda->save();

    $job = new AnalysePaySchedules($subMda->id);

    $action = Mockery::mock(AuditPayScheduleAction::class);
    $action->shouldNotReceive('execute');

    $job->handle($action);
});
