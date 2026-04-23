<?php

use App\Actions\GenerateAutopayOtherScheduleAction;
use App\Models\AuditOtherPaySchedule;
use App\Models\AutopayOtherSchedule;
use App\Models\Bank;
use App\Models\MicrofinanceOtherSchedule;
use App\Models\OtherAuditPayrollCategory;
use App\Models\PayComm;
use Tests\Feature\Actions\AutopayTestSetup;

uses(AutopayTestSetup::class);

it('creates autopay schedule for a commercial bank beneficiary with charges', function () {
    ['domain' => $domain, 'category' => $category] = buildOtherHierarchy($this, paycommEnabled: true);
    $bank = Bank::factory()->create(['code' => '058']);

    AuditOtherPaySchedule::create([
        'other_audit_payroll_category_id' => $category->id,
        'serial_number' => 1,
        'beneficiary_name' => 'JOHN DOE',
        'narration' => 'SALARY',
        'amount' => 50000,
        'account_number' => '1234567890',
        'bank_name' => $bank->name,
        'bank_code' => $bank->code,
        'bankable_type' => 'commercial',
        'bankable_id' => $bank->id,
    ]);

    (new GenerateAutopayOtherScheduleAction)->execute($category);

    // Beneficiary + PayComm I + PayComm II
    expect(AutopayOtherSchedule::all())->toHaveCount(3);

    $beneficiaryEntry = $category->autopaySchedules()->first();
    // amount (50000) − paycomm_i_charge (50) − paycomm_ii_charge (100) = 49850
    expect($beneficiaryEntry->amount)->toEqual(49850);
});

it('creates autopay schedule without charges when paycomm flags are off', function () {
    ['domain' => $domain, 'category' => $category] = buildOtherHierarchy($this, paycommEnabled: false);
    $bank = Bank::factory()->create();

    AuditOtherPaySchedule::create([
        'other_audit_payroll_category_id' => $category->id,
        'serial_number' => 1,
        'beneficiary_name' => 'JANE DOE',
        'narration' => 'ALLOWANCE',
        'amount' => 20000,
        'account_number' => '9876543210',
        'bank_name' => $bank->name,
        'bank_code' => $bank->code,
        'bankable_type' => 'commercial',
        'bankable_id' => $bank->id,
    ]);

    (new GenerateAutopayOtherScheduleAction)->execute($category);

    expect($category->autopaySchedules()->first()->amount)->toEqual(20000);
});

it('marks category as autopay generated', function () {
    ['domain' => $domain, 'category' => $category] = buildOtherHierarchy($this, paycommEnabled: true);
    $bank = Bank::factory()->create();

    AuditOtherPaySchedule::create([
        'other_audit_payroll_category_id' => $category->id,
        'serial_number' => 1,
        'beneficiary_name' => 'TEST PERSON',
        'narration' => 'TEST',
        'amount' => 10000,
        'account_number' => '1111111111',
        'bank_name' => $bank->name,
        'bank_code' => $bank->code,
        'bankable_type' => 'commercial',
        'bankable_id' => $bank->id,
    ]);

    (new GenerateAutopayOtherScheduleAction)->execute($category);

    expect($category->refresh()->autopay_generated)->not->toBeNull();
});

it('creates microfinance schedule for mfb beneficiary', function () {
    ['domain' => $domain, 'category' => $category] = buildOtherHierarchy($this, paycommEnabled: false);
    $this->createCashPaymentMfb($domain);
    $mfb = $this->createRealMfb($domain);

    AuditOtherPaySchedule::create([
        'other_audit_payroll_category_id' => $category->id,
        'serial_number' => 1,
        'beneficiary_name' => 'MFB PERSON',
        'narration' => 'SALARY',
        'amount' => 15000,
        'account_number' => '2222222222',
        'bank_name' => $mfb->name,
        'bank_code' => '999',
        'bankable_type' => 'micro_finance',
        'bankable_id' => $mfb->id,
    ]);

    (new GenerateAutopayOtherScheduleAction)->execute($category);

    expect(MicrofinanceOtherSchedule::all())->toHaveCount(1);
});

it('skips cash payment mfb beneficiaries', function () {
    ['domain' => $domain, 'category' => $category] = buildOtherHierarchy($this, paycommEnabled: false);
    $cashMfb = $this->createCashPaymentMfb($domain);

    AuditOtherPaySchedule::create([
        'other_audit_payroll_category_id' => $category->id,
        'serial_number' => 1,
        'beneficiary_name' => 'CASH PERSON',
        'narration' => 'CASH',
        'amount' => 5000,
        'account_number' => '3333333333',
        'bank_name' => 'CASH PAYMENT',
        'bank_code' => '000',
        'bankable_type' => 'micro_finance',
        'bankable_id' => $cashMfb->id,
    ]);

    (new GenerateAutopayOtherScheduleAction)->execute($category);

    expect(AutopayOtherSchedule::all())->toHaveCount(0);
    expect(MicrofinanceOtherSchedule::all())->toHaveCount(0);
});

// ── helpers ────────────────────────────────────────────────────────────

function buildOtherHierarchy(object $test, bool $paycommEnabled): array
{
    $domain = $test->createDomain();
    $user = $test->createUser($domain);
    $paymentType = $test->createPaymentType('all');
    $payroll = $test->createAuditPayroll($domain, $user);

    if ($paycommEnabled) {
        $test->createPayComms($domain);
    } else {
        $bank = Bank::factory()->create();
        PayComm::create(['code' => 'PayComm I',  'name' => 'PayComm I',  'account_number' => '1111111111', 'commission' => 0, 'bankable_type' => 'commercial', 'bankable_id' => $bank->id, 'domain_id' => $domain->id]);
        PayComm::create(['code' => 'PayComm II', 'name' => 'PayComm II', 'account_number' => '2222222222', 'commission' => 0, 'bankable_type' => 'commercial', 'bankable_id' => $bank->id, 'domain_id' => $domain->id]);
    }

    $category = OtherAuditPayrollCategory::create([
        'audit_payroll_id' => $payroll->id,
        'payment_type_id' => $paymentType->id,
        'payment_title' => 'ALL STAFF',
        'paycomm_tenece' => $paycommEnabled,
        'paycomm_fidelity' => $paycommEnabled,
    ]);

    return compact('domain', 'user', 'paymentType', 'payroll', 'category');
}
