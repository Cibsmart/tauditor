<?php

use App\Jobs\GenerateGroupSchedule;
use Illuminate\Contracts\Queue\Job;
use Illuminate\Queue\CallQueuedHandler;
use Tests\Feature\Actions\AutopayTestSetup;

uses(AutopayTestSetup::class);

it('declares deleteWhenMissingModels so a removed AuditPayrollCategory does not produce a failed job', function () {
    $domain = $this->createDomain();
    $payroll = $this->createAuditPayroll($domain, $this->createUser($domain));
    $job = new GenerateGroupSchedule(
        $domain,
        $this->createAuditPayrollCategory($payroll, $this->createPaymentType()),
        $this->createBeneficiaryType($domain),
    );

    expect($job->deleteWhenMissingModels)->toBeTrue();
});

it('is silently deleted by the worker when the AuditPayrollCategory is gone before processing', function () {
    $domain = $this->createDomain();
    $user = $this->createUser($domain);
    $paymentType = $this->createPaymentType();
    $beneficiaryType = $this->createBeneficiaryType($domain);
    $payroll = $this->createAuditPayroll($domain, $user);
    $category = $this->createAuditPayrollCategory($payroll, $paymentType);

    $serialized = serialize(new GenerateGroupSchedule($domain, $category, $beneficiaryType));

    // Simulate the dev-environment scenario: the row vanishes after the job
    // is queued but before the worker restores the model.
    $category->delete();

    $payload = [
        'displayName' => GenerateGroupSchedule::class,
        'job' => 'Illuminate\\Queue\\CallQueuedHandler@call',
        'data' => ['commandName' => GenerateGroupSchedule::class, 'command' => $serialized],
        'deleteWhenMissingModels' => true,
    ];

    $queueJob = Mockery::mock(Job::class);
    $queueJob->shouldReceive('payload')->andReturn($payload);
    $queueJob->shouldReceive('resolveQueuedJobClass')->andReturn(GenerateGroupSchedule::class);
    $queueJob->shouldReceive('uuid')->andReturn(null);
    $queueJob->shouldReceive('delete')->once();
    $queueJob->shouldNotReceive('fail');

    app(CallQueuedHandler::class)->call($queueJob, $payload['data']);
});
