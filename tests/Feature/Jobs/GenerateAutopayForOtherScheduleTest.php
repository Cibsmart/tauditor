<?php

use App\Actions\GenerateAutopayOtherScheduleAction;
use App\Jobs\GenerateAutopayForOtherSchedule;
use App\Models\OtherAuditPayrollCategory;
use Illuminate\Contracts\Queue\Job as QueueJob;
use Tests\Feature\Actions\AutopayTestSetup;
use Tests\TestCase;

uses(AutopayTestSetup::class);

function makeOtherCategory(AutopayTestSetup|TestCase $test): OtherAuditPayrollCategory
{
    $domain = $test->createDomain();
    $user = $test->createUser($domain);
    $paymentType = $test->createPaymentType('all');
    $payroll = $test->createAuditPayroll($domain, $user);

    return OtherAuditPayrollCategory::create([
        'audit_payroll_id' => $payroll->id,
        'payment_type_id' => $paymentType->id,
        'payment_title' => 'ALL STAFF',
    ]);
}

it('releases for a later attempt when the category row is not yet visible', function () {
    $job = new GenerateAutopayForOtherSchedule(999999);

    $queueJob = Mockery::mock(QueueJob::class);
    $queueJob->shouldReceive('release')->once()->with(5);
    $job->setJob($queueJob);

    $action = Mockery::mock(GenerateAutopayOtherScheduleAction::class);
    $action->shouldNotReceive('execute');

    $job->handle($action);
});

it('generates autopay when the category exists and is not yet generated', function () {
    $category = makeOtherCategory($this);

    $job = new GenerateAutopayForOtherSchedule($category->id);

    $action = Mockery::mock(GenerateAutopayOtherScheduleAction::class);
    $action->shouldReceive('execute')->once();

    $job->handle($action);
});

it('skips generation when autopay was already produced by a prior attempt', function () {
    $category = makeOtherCategory($this);
    $category->autopay_generated = now();
    $category->save();

    $job = new GenerateAutopayForOtherSchedule($category->id);

    $action = Mockery::mock(GenerateAutopayOtherScheduleAction::class);
    $action->shouldNotReceive('execute');

    $job->handle($action);
});

it('marks the category failed when generation fails terminally', function () {
    $category = makeOtherCategory($this);
    $category->setAutopayStatus('running');

    $job = new GenerateAutopayForOtherSchedule($category->id);
    $job->failed(new RuntimeException('boom'));

    expect($category->fresh()->autopay_status)->toBe('failed');
});

it('leaves an already completed status untouched on failure', function () {
    $category = makeOtherCategory($this);
    $category->setAutopayStatus('completed');

    $job = new GenerateAutopayForOtherSchedule($category->id);
    $job->failed(new RuntimeException('boom'));

    expect($category->fresh()->autopay_status)->toBe('completed');
});

it('does not throw when the category row is gone on failure', function () {
    $job = new GenerateAutopayForOtherSchedule(999999);

    $job->failed(new RuntimeException('boom'));
})->throwsNoExceptions();
