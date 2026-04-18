<?php

use App\Actions\GenerateAutoPayScheduleAction;
use App\Models\AuditPaySchedule;
use App\Models\AutopaySchedule;
use App\Models\Bank;
use App\Models\MicroFinanceBank;
use Tests\Feature\Actions\AutopayTestSetup;

uses(AutopayTestSetup::class);

it('creates autopay schedule for a commercial bank beneficiary', function () {
    ['domain' => $domain, 'subMda' => $subMda] = buildAutoPayHierarchy($this);
    ['payCommI' => $payCommI, 'payCommII' => $payCommII] = $this->createPayComms($domain);
    $this->createCashPaymentMfb($domain);

    $bank = Bank::factory()->create(['code' => '058']);
    createAutoPaySchedule($subMda->id, $bank, '1234567890', 50000);

    (new GenerateAutoPayScheduleAction())->execute($domain, $subMda);

    // Beneficiary + PayComm I + PayComm II = 3 entries
    expect($subMda->autopaySchedules)->toHaveCount(3);

    $beneficiaryEntry = $subMda->autopaySchedules()->first();
    // net_pay (50000) − paycomm_i_charge (50) − paycomm_ii_charge (100) = 49850
    expect($beneficiaryEntry->amount)->toEqual(49850);
});

it('creates paycomm i and paycomm ii consolidation entries', function () {
    ['domain' => $domain, 'subMda' => $subMda] = buildAutoPayHierarchy($this);
    ['payCommI' => $payCommI, 'payCommII' => $payCommII] = $this->createPayComms($domain);
    $this->createCashPaymentMfb($domain);

    $bank = Bank::factory()->create();
    createAutoPaySchedule($subMda->id, $bank, '0000000001', 60000);

    (new GenerateAutoPayScheduleAction())->execute($domain, $subMda);

    $accounts = $subMda->autopaySchedules->pluck('account_number')->toArray();
    expect($accounts)->toContain($payCommI->account_number);
    expect($accounts)->toContain($payCommII->account_number);
});

it('marks sub mda as autopay generated', function () {
    ['domain' => $domain, 'subMda' => $subMda] = buildAutoPayHierarchy($this);
    $this->createPayComms($domain);
    $this->createCashPaymentMfb($domain);

    $bank = Bank::factory()->create();
    createAutoPaySchedule($subMda->id, $bank, '5555555555', 40000);

    (new GenerateAutoPayScheduleAction())->execute($domain, $subMda);

    expect($subMda->refresh()->autopay_generated)->not->toBeNull();
});

it('links pay schedules to their autopay entries', function () {
    ['domain' => $domain, 'subMda' => $subMda] = buildAutoPayHierarchy($this);
    $this->createPayComms($domain);
    $this->createCashPaymentMfb($domain);

    $bank = Bank::factory()->create();
    $paySchedule = createAutoPaySchedule($subMda->id, $bank, '6666666666', 30000);

    (new GenerateAutoPayScheduleAction())->execute($domain, $subMda);

    expect($paySchedule->refresh()->autopay_schedule_id)->not->toBeNull();
});

it('skips cash payment mfb beneficiaries', function () {
    ['domain' => $domain, 'subMda' => $subMda] = buildAutoPayHierarchy($this);
    $this->createPayComms($domain);
    $cashMfb = $this->createCashPaymentMfb($domain);

    createMfbAutoPaySchedule($subMda->id, $cashMfb, '7777777777', 20000);

    (new GenerateAutoPayScheduleAction())->execute($domain, $subMda);

    // No beneficiary autopay entry should be created for a CASH PAYMENT schedule
    expect(AutopaySchedule::all())->toHaveCount(0);
});

// ── helpers ────────────────────────────────────────────────────────────

function buildAutoPayHierarchy(object $test): array
{
    $domain          = $test->createDomain();
    $user            = $test->createUser($domain);
    $paymentType     = $test->createPaymentType();
    $beneficiaryType = $test->createBeneficiaryType($domain);
    $mda             = $test->createMda($beneficiaryType);
    $payroll         = $test->createAuditPayroll($domain, $user);
    $category        = $test->createAuditPayrollCategory($payroll, $paymentType);
    $mdaSchedule     = $test->createAuditMdaSchedule($category, $mda);
    $subMda          = $test->createAuditSubMdaSchedule($mdaSchedule);

    return compact('domain', 'user', 'paymentType', 'beneficiaryType', 'mda', 'payroll', 'category', 'mdaSchedule', 'subMda');
}

function createAutoPaySchedule(int $subMdaId, Bank $bank, string $account, float $netPay, string $vn = 'VN001'): AuditPaySchedule
{
    return AuditPaySchedule::create([
        'audit_sub_mda_schedule_id' => $subMdaId,
        'verification_number'       => $vn,
        'beneficiary_name'          => 'TEST PERSON',
        'designation'               => 'Officer',
        'mda'                       => 'Test MDA',
        'department'                => 'Test Dept',
        'basic_pay'                 => $netPay * 0.6,
        'bank_name'                 => $bank->name,
        'account_number'            => $account,
        'bank_code'                 => $bank->code,
        'total_allowance'           => 0,
        'total_deductions'          => 0,
        'total_dues'                => 0,
        'total_dues_deductions'     => 0,
        'gross_pay'                 => $netPay,
        'net_pay'                   => $netPay,
        'allowances'                => [],
        'deductions'                => [],
        'dues'                      => [],
        'month'                     => '2024-03-01 00:00:00',
        'bankable_type'             => 'commercial',
        'bankable_id'               => $bank->id,
    ]);
}

function createMfbAutoPaySchedule(int $subMdaId, MicroFinanceBank $mfb, string $account, float $netPay): AuditPaySchedule
{
    return AuditPaySchedule::create([
        'audit_sub_mda_schedule_id' => $subMdaId,
        'verification_number'       => 'VN-MFB',
        'beneficiary_name'          => 'CASH PERSON',
        'designation'               => 'Officer',
        'mda'                       => 'Test MDA',
        'department'                => 'Test Dept',
        'basic_pay'                 => $netPay * 0.6,
        'bank_name'                 => $mfb->name,
        'account_number'            => $account,
        'bank_code'                 => '000',
        'total_allowance'           => 0,
        'total_deductions'          => 0,
        'total_dues'                => 0,
        'total_dues_deductions'     => 0,
        'gross_pay'                 => $netPay,
        'net_pay'                   => $netPay,
        'allowances'                => [],
        'deductions'                => [],
        'dues'                      => [],
        'month'                     => '2024-03-01 00:00:00',
        'bankable_type'             => 'micro_finance',
        'bankable_id'               => $mfb->id,
    ]);
}
