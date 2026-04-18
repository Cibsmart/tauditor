<?php

namespace Tests\Feature\Actions;

use App\Actions\GenerateAutoPayScheduleAction;
use App\Models\AuditPaySchedule;
use App\Models\AutopaySchedule;
use App\Models\Bank;
use App\Models\MicroFinanceBank;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GenerateAutoPayScheduleActionTest extends TestCase
{
    use RefreshDatabase, AutopayTestSetup;

    /** @test */
    public function it_creates_autopay_schedule_for_a_commercial_bank_beneficiary()
    {
        ['domain' => $domain, 'subMda' => $subMda] = $this->buildHierarchy();
        ['payCommI' => $payCommI, 'payCommII' => $payCommII] = $this->createPayComms($domain);
        $this->createCashPaymentMfb($domain);

        $bank = Bank::factory()->create(['code' => '058']);

        $this->createPaySchedule($subMda->id, $bank, '1234567890', 50000);

        (new GenerateAutoPayScheduleAction())->execute($domain, $subMda);

        // Beneficiary + PayComm I + PayComm II = 3 entries
        $this->assertCount(3, $subMda->autopaySchedules);

        $beneficiaryEntry = $subMda->autopaySchedules()->first();
        // net_pay (50000) − paycomm_i_charge (50) − paycomm_ii_charge (100) = 49850
        $this->assertEquals(49850, $beneficiaryEntry->amount);
    }

    /** @test */
    public function it_creates_paycomm_i_and_paycomm_ii_consolidation_entries()
    {
        ['domain' => $domain, 'subMda' => $subMda] = $this->buildHierarchy();
        ['payCommI' => $payCommI, 'payCommII' => $payCommII] = $this->createPayComms($domain);
        $this->createCashPaymentMfb($domain);

        $bank = Bank::factory()->create();
        $this->createPaySchedule($subMda->id, $bank, '0000000001', 60000);

        (new GenerateAutoPayScheduleAction())->execute($domain, $subMda);

        $accounts = $subMda->autopaySchedules->pluck('account_number')->toArray();
        $this->assertContains($payCommI->account_number, $accounts);
        $this->assertContains($payCommII->account_number, $accounts);
    }

    /** @test */
    public function it_marks_sub_mda_as_autopay_generated()
    {
        ['domain' => $domain, 'subMda' => $subMda] = $this->buildHierarchy();
        $this->createPayComms($domain);
        $this->createCashPaymentMfb($domain);

        $bank = Bank::factory()->create();
        $this->createPaySchedule($subMda->id, $bank, '5555555555', 40000);

        (new GenerateAutoPayScheduleAction())->execute($domain, $subMda);

        $subMda->refresh();
        $this->assertNotNull($subMda->autopay_generated);
    }

    /** @test */
    public function it_links_pay_schedules_to_their_autopay_entries()
    {
        ['domain' => $domain, 'subMda' => $subMda] = $this->buildHierarchy();
        $this->createPayComms($domain);
        $this->createCashPaymentMfb($domain);

        $bank = Bank::factory()->create();
        $paySchedule = $this->createPaySchedule($subMda->id, $bank, '6666666666', 30000);

        (new GenerateAutoPayScheduleAction())->execute($domain, $subMda);

        $paySchedule->refresh();
        $this->assertNotNull($paySchedule->autopay_schedule_id);
    }

    /** @test */
    public function it_skips_cash_payment_mfb_beneficiaries()
    {
        ['domain' => $domain, 'subMda' => $subMda] = $this->buildHierarchy();
        $this->createPayComms($domain);
        $cashMfb = $this->createCashPaymentMfb($domain);

        $this->createMfbPaySchedule($subMda->id, $cashMfb, '7777777777', 20000);

        (new GenerateAutoPayScheduleAction())->execute($domain, $subMda);

        // No beneficiary autopay entry should be created for a CASH PAYMENT schedule
        $this->assertCount(0, AutopaySchedule::all());
    }

    // ── helpers ────────────────────────────────────────────────────────────

    private function buildHierarchy(): array
    {
        $domain          = $this->createDomain();
        $user            = $this->createUser($domain);
        $paymentType     = $this->createPaymentType();
        $beneficiaryType = $this->createBeneficiaryType($domain);
        $mda             = $this->createMda($beneficiaryType);
        $payroll         = $this->createAuditPayroll($domain, $user);
        $category        = $this->createAuditPayrollCategory($payroll, $paymentType);
        $mdaSchedule     = $this->createAuditMdaSchedule($category, $mda);
        $subMda          = $this->createAuditSubMdaSchedule($mdaSchedule);

        return compact('domain', 'user', 'paymentType', 'beneficiaryType', 'mda', 'payroll', 'category', 'mdaSchedule', 'subMda');
    }

    private function createPaySchedule(int $subMdaId, Bank $bank, string $account, float $netPay, string $vn = 'VN001'): AuditPaySchedule
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

    private function createMfbPaySchedule(int $subMdaId, MicroFinanceBank $mfb, string $account, float $netPay): AuditPaySchedule
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
}
