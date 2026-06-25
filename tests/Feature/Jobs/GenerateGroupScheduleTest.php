<?php

use App\Actions\GenerateGroupAutopayScheduleAction;
use App\Jobs\GenerateGroupSchedule;
use Illuminate\Contracts\Queue\Job as QueueJob;
use Illuminate\Support\Facades\Cache;
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

it('releases for a later attempt when the same beneficiary type is already being generated', function () {
    $domain = $this->createDomain();
    $user = $this->createUser($domain);
    $paymentType = $this->createPaymentType();
    $payroll = $this->createAuditPayroll($domain, $user);
    $category = $this->createAuditPayrollCategory($payroll, $paymentType);
    $beneficiaryType = $this->createBeneficiaryType($domain);

    // Simulate a concurrent worker already holding the per-pair lock.
    Cache::lock("group-autopay:{$category->id}:{$beneficiaryType->id}", 200)->get();

    $job = new GenerateGroupSchedule($domain->id, $category->id, $beneficiaryType->id);

    $queueJob = Mockery::mock(QueueJob::class);
    $queueJob->shouldReceive('release')->once()->with(5);
    $job->setJob($queueJob);

    $action = Mockery::mock(GenerateGroupAutopayScheduleAction::class);
    $action->shouldNotReceive('execute');

    $job->handle($action);
});

it('marks the category failed when generation fails terminally', function () {
    $domain = $this->createDomain();
    $user = $this->createUser($domain);
    $paymentType = $this->createPaymentType();
    $payroll = $this->createAuditPayroll($domain, $user);
    $category = $this->createAuditPayrollCategory($payroll, $paymentType);
    $beneficiaryType = $this->createBeneficiaryType($domain);
    $category->setAutopayStatus('running');

    $job = new GenerateGroupSchedule($domain->id, $category->id, $beneficiaryType->id);
    $job->failed(new RuntimeException('boom'));

    expect($category->fresh()->autopay_status)->toBe('failed');
});

it('does not throw when the category row is gone on failure', function () {
    $job = new GenerateGroupSchedule('missing-domain', 999999, 'missing-type');

    $job->failed(new RuntimeException('boom'));
})->throwsNoExceptions();
