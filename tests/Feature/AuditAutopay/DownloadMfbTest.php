<?php

use App\Jobs\BuildMfbScheduleZip;
use App\Models\MicrofinanceBankSchedule;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;
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

it('redirects with error when category has no autopay schedule', function () {
    ['user' => $user, 'category' => $category] = buildMfbDownloadHierarchy($this);

    $response = $this->actingAs($user)
        ->get(route('audit_autopay.downloadMfb', $category));

    $response->assertRedirect();
    expect(session('error'))->toContain('yet to be Generated');
});

it('redirects with error when category has autopay but no mfb schedule', function () {
    ['user' => $user, 'category' => $category, 'subMda' => $subMda] = buildMfbDownloadHierarchy($this);

    $subMda->autopay_generated = now();
    $subMda->save();

    $response = $this->actingAs($user)
        ->get(route('audit_autopay.downloadMfb', $category));

    $response->assertRedirect();
    expect(session('error'))->toContain('No Beneficiary Used Microfinance');
});

it('dispatches the build job on first click and marks cache as running', function () {
    Bus::fake();

    ['user' => $user, 'category' => $category, 'subMda' => $subMda, 'domain' => $domain] = buildMfbDownloadHierarchy($this);
    $subMda->autopay_generated = now();
    $subMda->save();
    $mfb = $this->createRealMfb($domain);
    createMfbSchedule($subMda->id, $mfb->id);

    $response = $this->actingAs($user)
        ->get(route('audit_autopay.downloadMfb', $category));

    $response->assertRedirect();
    expect(session('success'))->toContain('being prepared');

    Bus::assertDispatched(
        BuildMfbScheduleZip::class,
        fn (BuildMfbScheduleZip $job) => $job->category->id === $category->id,
    );
    expect(Cache::get(BuildMfbScheduleZip::statusKey($category)))->toBe('running');
});

it('does not dispatch a second job while one is already running', function () {
    Bus::fake();

    ['user' => $user, 'category' => $category, 'subMda' => $subMda, 'domain' => $domain] = buildMfbDownloadHierarchy($this);
    $subMda->autopay_generated = now();
    $subMda->save();
    $mfb = $this->createRealMfb($domain);
    createMfbSchedule($subMda->id, $mfb->id);

    Cache::put(BuildMfbScheduleZip::statusKey($category), 'running', now()->addHour());

    $response = $this->actingAs($user)
        ->get(route('audit_autopay.downloadMfb', $category));

    $response->assertRedirect();
    expect(session('success'))->toContain('being prepared');

    Bus::assertNothingDispatched();
});

it('serves the prebuilt zip when one already exists on disk', function () {
    ['user' => $user, 'category' => $category, 'subMda' => $subMda, 'domain' => $domain] = buildMfbDownloadHierarchy($this);
    $subMda->autopay_generated = now();
    $subMda->save();
    $mfb = $this->createRealMfb($domain);
    createMfbSchedule($subMda->id, $mfb->id);

    $zipPath = BuildMfbScheduleZip::zipPath($category);
    @mkdir(dirname($zipPath), 0755, true);
    file_put_contents($zipPath, "PK\x03\x04fake-zip-payload");

    $response = $this->actingAs($user)
        ->get(route('audit_autopay.downloadMfb', $category));

    $response->assertOk();
    expect($response->headers->get('Content-Type'))->toBe('application/zip');
    expect(substr($response->streamedContent(), 0, 4))->toBe("PK\x03\x04");
});

it('builds a valid zip with mfb files when the job runs', function () {
    ['category' => $category, 'subMda' => $subMda, 'domain' => $domain] = buildMfbDownloadHierarchy($this);
    $subMda->autopay_generated = now();
    $subMda->save();
    $mfb = $this->createRealMfb($domain);
    createMfbSchedule($subMda->id, $mfb->id);

    (new BuildMfbScheduleZip($category))->handle();

    $path = BuildMfbScheduleZip::zipPath($category);
    expect(file_exists($path))->toBeTrue();
    expect(substr(file_get_contents($path), 0, 4))->toBe("PK\x03\x04");
    expect(Cache::get(BuildMfbScheduleZip::statusKey($category)))->toBeNull();
});
