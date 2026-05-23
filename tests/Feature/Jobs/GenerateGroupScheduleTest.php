<?php

use App\Actions\GenerateGroupAutopayScheduleAction;
use App\Jobs\GenerateGroupSchedule;
use Illuminate\Contracts\Queue\Job as QueueJob;
use Tests\Feature\Actions\AutopayTestSetup;

uses(AutopayTestSetup::class);

it('releases for a later attempt when a bound row is not yet visible', function () {
    $job = new GenerateGroupSchedule('missing-domain', 999999, 'missing-type');

    $queueJob = Mockery::mock(QueueJob::class);
    $queueJob->shouldReceive('release')->once()->with(5);
    $job->setJob($queueJob);

    $action = Mockery::mock(GenerateGroupAutopayScheduleAction::class);
    $action->shouldNotReceive('execute');

    $job->handle($action);
});

it('generates the group schedule when the bound rows exist and are not yet generated', function () {
    $domain = $this->createDomain();
    $user = $this->createUser($domain);
    $paymentType = $this->createPaymentType();
    $payroll = $this->createAuditPayroll($domain, $user);
    $category = $this->createAuditPayrollCategory($payroll, $paymentType);
    $beneficiaryType = $this->createBeneficiaryType($domain);

    $job = new GenerateGroupSchedule($domain->id, $category->id, $beneficiaryType->id);

    $action = Mockery::mock(GenerateGroupAutopayScheduleAction::class);
    $action->shouldReceive('execute')->once();

    $job->handle($action);
});
