<?php

use App\Models\MicrofinanceBankSchedule;
use Tests\Feature\Actions\AutopayTestSetup;

uses(AutopayTestSetup::class);

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

it('streams a zip when category has mfb schedules', function () {
    ['user' => $user, 'category' => $category, 'subMda' => $subMda, 'domain' => $domain] = buildMfbDownloadHierarchy($this);

    $subMda->autopay_generated = now();
    $subMda->save();

    $mfb = $this->createRealMfb($domain);

    MicrofinanceBankSchedule::create([
        'audit_sub_mda_schedule_id' => $subMda->id,
        'micro_finance_bank_id' => $mfb->id,
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

    $response = $this->actingAs($user)
        ->get(route('audit_autopay.downloadMfb', $category));

    $response->assertOk();
    expect($response->headers->get('Content-Type'))->toBe('application/zip');

    $body = $response->streamedContent();
    expect(substr($body, 0, 4))->toBe("PK\x03\x04");
});
