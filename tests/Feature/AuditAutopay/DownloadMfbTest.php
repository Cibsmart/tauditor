<?php

use App\Jobs\BuildMfbScheduleZip;
use App\Models\MicrofinanceBankSchedule;
use App\Models\ScheduleZip;
use Illuminate\Support\Facades\Bus;
use Inertia\Testing\AssertableInertia;
use Tests\Feature\Actions\AutopayTestSetup;

uses(AutopayTestSetup::class);

afterEach(function () {
    foreach (glob(storage_path('app/mfb_exports/*')) ?: [] as $file) {
        @unlink($file);
    }
});

function buildMfbDownloadHierarchy(object $test): array
{
    $domain = $test->createDomain();
    $user = $test->createUser($domain);
    $paymentType = $test->createPaymentType();
    $beneficiaryType = $test->createBeneficiaryType($domain);
    $mda = $test->createMda($beneficiaryType);
    $payroll = $test->createAuditPayroll($domain, $user);
    $category = $test->createAuditPayrollCategory($payroll, $paymentType);
    $mdaSchedule = $test->createAuditMdaSchedule($category, $mda);
    $subMda = $test->createAuditSubMdaSchedule($mdaSchedule);

    return compact('domain', 'user', 'category', 'subMda');
}

function createMfbSchedule(int $subMdaId, int $mfbId): MicrofinanceBankSchedule
{
    return MicrofinanceBankSchedule::create([
        'audit_sub_mda_schedule_id' => $subMdaId,
        'micro_finance_bank_id' => $mfbId,
        'payment_reference' => 'PR-001',
        'beneficiary_code' => 'BC-001',
        'beneficiary_name' => 'TEST BENEFICIARY',
        'account_number' => '1234567890',
        'account_type' => '10',
        'cbn_code' => '999',
        'is_cash_card' => 'N',
        'narration' => 'Salary',
        'amount' => '50000',
        'email' => 'test@example.com',
        'currency' => 'NGN',
    ]);
}

it('redirects with error when build is requested for a category with no autopay schedule', function () {
    ['user' => $user, 'category' => $category] = buildMfbDownloadHierarchy($this);

    $response = $this->actingAs($user)
        ->post(route('audit_autopay.buildMfb', $category));

    $response->assertRedirect();
    expect(session('error'))->toContain('yet to be Generated');
});

it('redirects with error when build is requested for a category with no mfb schedule', function () {
    ['user' => $user, 'category' => $category, 'subMda' => $subMda] = buildMfbDownloadHierarchy($this);

    $subMda->autopay_generated = now();
    $subMda->save();

    $response = $this->actingAs($user)
        ->post(route('audit_autopay.buildMfb', $category));

    $response->assertRedirect();
    expect(session('error'))->toContain('No Beneficiary Used Microfinance');
});

it('dispatches the build job on first POST and marks status as building', function () {
    Bus::fake();

    [
        'user' => $user, 'category' => $category, 'subMda' => $subMda, 'domain' => $domain,
    ] = buildMfbDownloadHierarchy($this);
    $subMda->autopay_generated = now();
    $subMda->save();
    $mfb = $this->createRealMfb($domain);
    createMfbSchedule($subMda->id, $mfb->id);

    $response = $this->actingAs($user)
        ->post(route('audit_autopay.buildMfb', $category));

    $response->assertRedirect();
    expect(session('success'))->toContain('being prepared');

    Bus::assertDispatched(
        BuildMfbScheduleZip::class,
        fn (BuildMfbScheduleZip $job) => $job->category->id === $category->id,
    );
    expect(ScheduleZip::where('audit_payroll_category_id', $category->id)
        ->where('type', ScheduleZip::TYPE_MFB)->first()?->status)
        ->toBe(ScheduleZip::STATUS_BUILDING);
});

it('does not dispatch a second job while one is already running and does not stomp the row', function () {
    Bus::fake();

    [
        'user' => $user, 'category' => $category, 'subMda' => $subMda, 'domain' => $domain,
    ] = buildMfbDownloadHierarchy($this);
    $subMda->autopay_generated = now();
    $subMda->save();
    $mfb = $this->createRealMfb($domain);
    createMfbSchedule($subMda->id, $mfb->id);

    ScheduleZip::create([
        'audit_payroll_category_id' => $category->id,
        'type' => ScheduleZip::TYPE_MFB,
        'status' => ScheduleZip::STATUS_BUILDING,
    ]);

    $response = $this->actingAs($user)
        ->post(route('audit_autopay.buildMfb', $category));

    $response->assertRedirect();
    Bus::assertNothingDispatched();
    expect(ScheduleZip::where('audit_payroll_category_id', $category->id)
        ->where('type', ScheduleZip::TYPE_MFB)->first()?->status)
        ->toBe(ScheduleZip::STATUS_BUILDING);
});

it('serves the prebuilt zip on GET download and does not delete it after send', function () {
    [
        'user' => $user, 'category' => $category, 'subMda' => $subMda, 'domain' => $domain,
    ] = buildMfbDownloadHierarchy($this);
    $subMda->autopay_generated = now();
    $subMda->save();
    $mfb = $this->createRealMfb($domain);
    createMfbSchedule($subMda->id, $mfb->id);

    $zipPath = ScheduleZip::pathFor($category->id, ScheduleZip::TYPE_MFB);
    @mkdir(dirname($zipPath), 0755, true);
    file_put_contents($zipPath, "PK\x03\x04fake-zip-payload");

    ScheduleZip::create([
        'audit_payroll_category_id' => $category->id,
        'type' => ScheduleZip::TYPE_MFB,
        'status' => ScheduleZip::STATUS_READY,
        'built_at' => now(),
    ]);

    $response = $this->actingAs($user)
        ->get(route('audit_autopay.downloadMfb', $category));

    $response->assertOk();
    expect($response->headers->get('Content-Type'))->toBe('application/zip');
    expect(substr($response->streamedContent(), 0, 4))->toBe("PK\x03\x04");
    expect(file_exists($zipPath))->toBeTrue();
});

it('redirects with error on GET download when the zip is not yet built', function () {
    [
        'user' => $user, 'category' => $category, 'subMda' => $subMda, 'domain' => $domain,
    ] = buildMfbDownloadHierarchy($this);
    $subMda->autopay_generated = now();
    $subMda->save();
    $mfb = $this->createRealMfb($domain);
    createMfbSchedule($subMda->id, $mfb->id);

    $response = $this->actingAs($user)
        ->get(route('audit_autopay.downloadMfb', $category));

    $response->assertRedirect();
    expect(session('error'))->toContain('not ready');
});

it('builds a valid zip with mfb files when the job runs', function () {
    ['category' => $category, 'subMda' => $subMda, 'domain' => $domain] = buildMfbDownloadHierarchy($this);
    $subMda->autopay_generated = now();
    $subMda->save();
    $mfb = $this->createRealMfb($domain);
    createMfbSchedule($subMda->id, $mfb->id);

    ScheduleZip::create([
        'audit_payroll_category_id' => $category->id,
        'type' => ScheduleZip::TYPE_MFB,
        'status' => ScheduleZip::STATUS_BUILDING,
    ]);

    (new BuildMfbScheduleZip($category))->handle();

    $path = ScheduleZip::pathFor($category->id, ScheduleZip::TYPE_MFB);
    expect(file_exists($path))->toBeTrue();
    expect(substr(file_get_contents($path), 0, 4))->toBe("PK\x03\x04");

    $row = ScheduleZip::where('audit_payroll_category_id', $category->id)
        ->where('type', ScheduleZip::TYPE_MFB)->first();
    expect($row->status)->toBe(ScheduleZip::STATUS_READY);
    expect($row->built_at)->not->toBeNull();
});

it('surfaces mfb_zip_status=building in the index payload while a job is running', function () {
    [
        'user' => $user, 'category' => $category, 'subMda' => $subMda, 'domain' => $domain,
    ] = buildMfbDownloadHierarchy($this);
    $subMda->autopay_generated = now();
    $subMda->save();
    $mfb = $this->createRealMfb($domain);
    createMfbSchedule($subMda->id, $mfb->id);

    ScheduleZip::create([
        'audit_payroll_category_id' => $category->id,
        'type' => ScheduleZip::TYPE_MFB,
        'status' => ScheduleZip::STATUS_BUILDING,
    ]);

    $response = $this->actingAs($user)
        ->get(route('audit_autopay.index'));

    $response->assertInertia(fn (AssertableInertia $page) => $page
        ->component('AuditAutoPay/Index')
        ->where('payrolls.data.0.categories.0.has_mfb_schedule', true)
        ->where('payrolls.data.0.categories.0.mfb_zip_status', 'building'),
    );
});

it('surfaces mfb_zip_status=failed in the index payload after a failed build', function () {
    [
        'user' => $user, 'category' => $category, 'subMda' => $subMda, 'domain' => $domain,
    ] = buildMfbDownloadHierarchy($this);
    $subMda->autopay_generated = now();
    $subMda->save();
    $mfb = $this->createRealMfb($domain);
    createMfbSchedule($subMda->id, $mfb->id);

    ScheduleZip::create([
        'audit_payroll_category_id' => $category->id,
        'type' => ScheduleZip::TYPE_MFB,
        'status' => ScheduleZip::STATUS_FAILED,
        'failed_at' => now(),
        'failure_reason' => 'boom',
    ]);

    $response = $this->actingAs($user)
        ->get(route('audit_autopay.index'));

    $response->assertInertia(fn (AssertableInertia $page) => $page
        ->where('payrolls.data.0.categories.0.mfb_zip_status', 'failed'),
    );
});

it('surfaces mfb_zip_status=ready when the zip exists on disk', function () {
    [
        'user' => $user, 'category' => $category, 'subMda' => $subMda, 'domain' => $domain,
    ] = buildMfbDownloadHierarchy($this);
    $subMda->autopay_generated = now();
    $subMda->save();
    $mfb = $this->createRealMfb($domain);
    createMfbSchedule($subMda->id, $mfb->id);

    $zipPath = ScheduleZip::pathFor($category->id, ScheduleZip::TYPE_MFB);
    @mkdir(dirname($zipPath), 0755, true);
    file_put_contents($zipPath, "PK\x03\x04fake-zip-payload");

    ScheduleZip::create([
        'audit_payroll_category_id' => $category->id,
        'type' => ScheduleZip::TYPE_MFB,
        'status' => ScheduleZip::STATUS_READY,
        'built_at' => now(),
    ]);

    $response = $this->actingAs($user)
        ->get(route('audit_autopay.index'));

    $response->assertInertia(fn (AssertableInertia $page) => $page
        ->where('payrolls.data.0.categories.0.mfb_zip_status', 'ready')
        ->where('payrolls.data.0.categories.0.has_mfb_schedule', true),
    );
});

it('surfaces has_mfb_schedule=false when category has no mfb schedule rows', function () {
    ['user' => $user, 'category' => $category, 'subMda' => $subMda] = buildMfbDownloadHierarchy($this);
    $subMda->autopay_generated = now();
    $subMda->save();

    $response = $this->actingAs($user)
        ->get(route('audit_autopay.index'));

    $response->assertInertia(fn (AssertableInertia $page) => $page
        ->where('payrolls.data.0.categories.0.has_mfb_schedule', false)
        ->where('payrolls.data.0.categories.0.mfb_zip_status', 'none'),
    );
});

it('marks status as failed when the job failed() lifecycle hook fires', function () {
    ['category' => $category] = buildMfbDownloadHierarchy($this);

    $job = new BuildMfbScheduleZip($category);
    $job->failed(new RuntimeException('boom'));

    $row = ScheduleZip::where('audit_payroll_category_id', $category->id)
        ->where('type', ScheduleZip::TYPE_MFB)->first();
    expect($row->status)->toBe(ScheduleZip::STATUS_FAILED);
    expect($row->failure_reason)->toContain('boom');
});

it('falls back to mfb_zip_status=none when DB row says ready but file is missing on disk', function () {
    [
        'user' => $user, 'category' => $category, 'subMda' => $subMda, 'domain' => $domain,
    ] = buildMfbDownloadHierarchy($this);
    $subMda->autopay_generated = now();
    $subMda->save();
    $mfb = $this->createRealMfb($domain);
    createMfbSchedule($subMda->id, $mfb->id);

    ScheduleZip::create([
        'audit_payroll_category_id' => $category->id,
        'type' => ScheduleZip::TYPE_MFB,
        'status' => ScheduleZip::STATUS_READY,
        'built_at' => now(),
    ]);

    $response = $this->actingAs($user)
        ->get(route('audit_autopay.index'));

    $response->assertInertia(fn (AssertableInertia $page) => $page
        ->where('payrolls.data.0.categories.0.mfb_zip_status', 'none'),
    );
    expect(ScheduleZip::where('audit_payroll_category_id', $category->id)
        ->where('type', ScheduleZip::TYPE_MFB)->count())->toBe(0);
});

it('retries from a failed status by flipping the row back to building', function () {
    Bus::fake();

    [
        'user' => $user, 'category' => $category, 'subMda' => $subMda, 'domain' => $domain,
    ] = buildMfbDownloadHierarchy($this);
    $subMda->autopay_generated = now();
    $subMda->save();
    $mfb = $this->createRealMfb($domain);
    createMfbSchedule($subMda->id, $mfb->id);

    ScheduleZip::create([
        'audit_payroll_category_id' => $category->id,
        'type' => ScheduleZip::TYPE_MFB,
        'status' => ScheduleZip::STATUS_FAILED,
        'failed_at' => now(),
        'failure_reason' => 'previous attempt blew up',
    ]);

    $response = $this->actingAs($user)
        ->post(route('audit_autopay.buildMfb', $category));

    $response->assertRedirect();
    Bus::assertDispatched(BuildMfbScheduleZip::class);

    $row = ScheduleZip::where('audit_payroll_category_id', $category->id)
        ->where('type', ScheduleZip::TYPE_MFB)->first();
    expect($row->status)->toBe(ScheduleZip::STATUS_BUILDING);
    expect($row->failed_at)->toBeNull();
    expect($row->failure_reason)->toBeNull();
});

it('is a no-op when buildMfb is POSTed and status is already ready', function () {
    Bus::fake();

    [
        'user' => $user, 'category' => $category, 'subMda' => $subMda, 'domain' => $domain,
    ] = buildMfbDownloadHierarchy($this);
    $subMda->autopay_generated = now();
    $subMda->save();
    $mfb = $this->createRealMfb($domain);
    createMfbSchedule($subMda->id, $mfb->id);

    $zipPath = ScheduleZip::pathFor($category->id, ScheduleZip::TYPE_MFB);
    @mkdir(dirname($zipPath), 0755, true);
    file_put_contents($zipPath, "PK\x03\x04fake-zip-payload");

    $builtAt = now()->subMinutes(10);
    ScheduleZip::create([
        'audit_payroll_category_id' => $category->id,
        'type' => ScheduleZip::TYPE_MFB,
        'status' => ScheduleZip::STATUS_READY,
        'built_at' => $builtAt,
    ]);

    $response = $this->actingAs($user)
        ->post(route('audit_autopay.buildMfb', $category));

    $response->assertRedirect();
    Bus::assertNothingDispatched();

    $row = ScheduleZip::where('audit_payroll_category_id', $category->id)
        ->where('type', ScheduleZip::TYPE_MFB)->first();
    expect($row->status)->toBe(ScheduleZip::STATUS_READY);
    expect($row->built_at->timestamp)->toBe($builtAt->timestamp);
});
