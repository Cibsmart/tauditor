<?php

use App\Actions\GenerateGroupAutopayScheduleAction;
use App\Models\AuditMdaSchedule;
use App\Models\AuditPaySchedule;
use App\Models\AuditSubMdaSchedule;
use App\Models\AutopaySchedule;
use App\Models\Bank;
use App\Models\BeneficiaryType;
use App\Models\Domain;
use Tests\Feature\Actions\AutopayTestSetup;

uses(AutopayTestSetup::class);

it('creates autopay schedules for commercial bank beneficiaries', function () {
    [
        'domain' => $domain,
        'category' => $category,
        'beneficiaryType' => $beneficiaryType,
        'subMda' => $subMda,
    ] = buildGroupHierarchy($this);

    $bank = Bank::factory()->create(['code' => '058']);

    AuditPaySchedule::create([
        'audit_sub_mda_schedule_id' => $subMda->id,
        'verification_number' => 'VN001',
        'beneficiary_name' => 'JOHN DOE',
        'designation' => 'Officer',
        'mda' => 'Test MDA',
        'department' => 'Test Dept',
        'basic_pay' => 30000,
        'bank_name' => $bank->name,
        'account_number' => '1234567890',
        'bank_code' => $bank->code,
        'total_allowance' => 0,
        'total_deductions' => 0,
        'total_dues' => 0,
        'total_dues_deductions' => 0,
        'gross_pay' => 50000,
        'net_pay' => 50000,
        'allowances' => [],
        'deductions' => [],
        'dues' => [],
        'month' => '2024-03-01 00:00:00',
        'bankable_type' => 'commercial',
        'bankable_id' => $bank->id,
    ]);

    (new GenerateGroupAutopayScheduleAction)->execute($domain, $category, $beneficiaryType);

    // Beneficiary + PayComm I + PayComm II
    expect(AutopaySchedule::all())->toHaveCount(3);

    $beneficiaryEntry = $subMda->autopaySchedules()->orderBy('id')->first();
    // net_pay (50000) − paycomm_i (50) − paycomm_ii (100) = 49850
    expect($beneficiaryEntry->amount)->toEqual(49850);
});

it('marks all touched sub mdas as autopay generated', function () {
    [
        'domain' => $domain,
        'category' => $category,
        'beneficiaryType' => $beneficiaryType,
        'subMda' => $subMda,
    ] = buildGroupHierarchy($this);

    $bank = Bank::factory()->create();

    AuditPaySchedule::create([
        'audit_sub_mda_schedule_id' => $subMda->id,
        'verification_number' => 'VN002',
        'beneficiary_name' => 'TEST PERSON',
        'designation' => 'Officer',
        'mda' => 'Test MDA',
        'department' => 'Test Dept',
        'basic_pay' => 20000,
        'bank_name' => $bank->name,
        'account_number' => '9999999999',
        'bank_code' => $bank->code,
        'total_allowance' => 0,
        'total_deductions' => 0,
        'total_dues' => 0,
        'total_dues_deductions' => 0,
        'gross_pay' => 40000,
        'net_pay' => 40000,
        'allowances' => [],
        'deductions' => [],
        'dues' => [],
        'month' => '2024-03-01 00:00:00',
        'bankable_type' => 'commercial',
        'bankable_id' => $bank->id,
    ]);

    (new GenerateGroupAutopayScheduleAction)->execute($domain, $category, $beneficiaryType);

    expect($subMda->refresh()->autopay_generated)->not->toBeNull();
});

it('only processes schedules matching the given beneficiary type', function () {
    [
        'domain' => $domain,
        'category' => $category,
        'beneficiaryType' => $targetType,
        'mdaSchedule' => $mdaSchedule,
    ] = buildGroupHierarchy($this);

    // A second sub_mda under a different MDA whose beneficiary type differs
    $otherType = createSecondBeneficiaryType($domain);
    $otherMda = $this->createMda($otherType);
    $otherMdaSchedule = $this->createAuditMdaSchedule($category, $otherMda);
    $otherSubMda = $this->createAuditSubMdaSchedule($otherMdaSchedule);

    $bank = Bank::factory()->create();

    // Schedule under the target beneficiary type
    AuditPaySchedule::create([
        'audit_sub_mda_schedule_id' => subMdaFor($mdaSchedule)->id,
        'verification_number' => 'VN003',
        'beneficiary_name' => 'TARGET PERSON',
        'designation' => 'Officer',
        'mda' => 'Test MDA',
        'department' => 'Test Dept',
        'basic_pay' => 10000,
        'bank_name' => $bank->name,
        'account_number' => '5555555555',
        'bank_code' => $bank->code,
        'total_allowance' => 0,
        'total_deductions' => 0,
        'total_dues' => 0,
        'total_dues_deductions' => 0,
        'gross_pay' => 20000,
        'net_pay' => 20000,
        'allowances' => [],
        'deductions' => [],
        'dues' => [],
        'month' => '2024-03-01 00:00:00',
        'bankable_type' => 'commercial',
        'bankable_id' => $bank->id,
    ]);

    // Schedule under the OTHER beneficiary type (should be ignored)
    AuditPaySchedule::create([
        'audit_sub_mda_schedule_id' => $otherSubMda->id,
        'verification_number' => 'VN004',
        'beneficiary_name' => 'OTHER PERSON',
        'designation' => 'Officer',
        'mda' => 'Other MDA',
        'department' => 'Other Dept',
        'basic_pay' => 10000,
        'bank_name' => $bank->name,
        'account_number' => '9876543210',
        'bank_code' => $bank->code,
        'total_allowance' => 0,
        'total_deductions' => 0,
        'total_dues' => 0,
        'total_dues_deductions' => 0,
        'gross_pay' => 20000,
        'net_pay' => 20000,
        'allowances' => [],
        'deductions' => [],
        'dues' => [],
        'month' => '2024-03-01 00:00:00',
        'bankable_type' => 'commercial',
        'bankable_id' => $bank->id,
    ]);

    (new GenerateGroupAutopayScheduleAction)->execute($domain, $category, $targetType);

    // Only the one beneficiary matching targetType + PayComm I + PayComm II = 3
    expect(AutopaySchedule::all())->toHaveCount(3);
    $accounts = AutopaySchedule::pluck('account_number')->toArray();
    expect($accounts)->toContain('5555555555');
    expect($accounts)->not->toContain('9876543210');
});

// ── helpers ────────────────────────────────────────────────────────────

function buildGroupHierarchy(object $test): array
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

    $test->createPayComms($domain);
    $test->createCashPaymentMfb($domain);

    return compact('domain', 'user', 'paymentType', 'beneficiaryType', 'mda', 'payroll', 'category', 'mdaSchedule', 'subMda');
}

function createSecondBeneficiaryType(Domain $domain): BeneficiaryType
{
    return BeneficiaryType::create([
        'id' => 'bt-other',
        'name' => 'Other Type',
        'domain_id' => $domain->id,
    ]);
}

function subMdaFor(AuditMdaSchedule $mdaSchedule): AuditSubMdaSchedule
{
    return $mdaSchedule->auditSubMdaSchedules()->firstOrFail();
}
