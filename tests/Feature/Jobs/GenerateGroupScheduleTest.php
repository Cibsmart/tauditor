<?php

use App\Jobs\GenerateGroupSchedule;
use Illuminate\Contracts\Queue\Job;
use Illuminate\Queue\CallQueuedHandler;
use Tests\Feature\Actions\AutopayTestSetup;

uses(AutopayTestSetup::class);

it('does not silently discard missing models so failures are visible in Horizon', function () {
    $domain = $this->createDomain();
    $payroll = $this->createAuditPayroll($domain, $this->createUser($domain));
    $job = new GenerateGroupSchedule(
        $domain,
        $this->createAuditPayrollCategory($payroll, $this->createPaymentType()),
        $this->createBeneficiaryType($domain),
    );

    expect(isset($job->deleteWhenMissingModels) && $job->deleteWhenMissingModels)->toBeFalse();
});

it('fails visibly when the AuditPayrollCategory is gone before processing', function () {
    $domain = $this->createDomain();
    $user = $this->createUser($domain);
    $paymentType = $this->createPaymentType();
    $beneficiaryType = $this->createBeneficiaryType($domain);
    $payroll = $this->createAuditPayroll($domain, $user);
    $category = $this->createAuditPayrollCategory($payroll, $paymentType);

    $serialized = serialize(new GenerateGroupSchedule($domain, $category, $beneficiaryType));

    $category->delete();

    $payload = [
        'displayName' => GenerateGroupSchedule::class,
        'job' => 'Illuminate\\Queue\\CallQueuedHandler@call',
        'data' => ['commandName' => GenerateGroupSchedule::class, 'command' => $serialized],
    ];

    $queueJob = Mockery::mock(Job::class);
    $queueJob->shouldReceive('payload')->andReturn($payload);
    $queueJob->shouldReceive('resolveQueuedJobClass')->andReturn(GenerateGroupSchedule::class);
    $queueJob->shouldReceive('uuid')->andReturn(null);
    $queueJob->shouldReceive('fail')->once();
    $queueJob->shouldNotReceive('delete');

    app(CallQueuedHandler::class)->call($queueJob, $payload['data']);
});
