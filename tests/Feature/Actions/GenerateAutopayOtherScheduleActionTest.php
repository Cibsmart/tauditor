<?php

namespace Tests\Feature\Actions;

use App\Actions\GenerateAutopayOtherScheduleAction;
use App\Models\AuditOtherPaySchedule;
use App\Models\AuditPayroll;
use App\Models\AutopayOtherSchedule;
use App\Models\Bank;
use App\Models\MicrofinanceOtherSchedule;
use App\Models\OtherAuditPayrollCategory;
use App\Models\PayComm;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GenerateAutopayOtherScheduleActionTest extends TestCase
{
    use RefreshDatabase, AutopayTestSetup;

    /** @test */
    public function it_creates_autopay_schedule_for_a_commercial_bank_beneficiary_with_charges()
    {
        ['domain' => $domain, 'category' => $category] = $this->buildHierarchy(paycommEnabled: true);
        $bank = factory(Bank::class)->create(['code' => '058']);

        AuditOtherPaySchedule::create([
            'other_audit_payroll_category_id' => $category->id,
            'serial_number'                   => 1,
            'beneficiary_name'                => 'JOHN DOE',
            'narration'                       => 'SALARY',
            'amount'                          => 50000,
            'account_number'                  => '1234567890',
            'bank_name'                       => $bank->name,
            'bank_code'                       => $bank->code,
            'bankable_type'                   => 'commercial',
            'bankable_id'                     => $bank->id,
        ]);

        (new GenerateAutopayOtherScheduleAction())->execute($category);

        // Beneficiary + PayComm I + PayComm II
        $this->assertCount(3, AutopayOtherSchedule::all());

        $beneficiaryEntry = $category->autopaySchedules()->first();
        // amount (50000) − paycomm_i_charge (50) − paycomm_ii_charge (100) = 49850
        $this->assertEquals(49850, $beneficiaryEntry->amount);
    }

    /** @test */
    public function it_creates_autopay_schedule_without_charges_when_paycomm_flags_are_off()
    {
        ['domain' => $domain, 'category' => $category] = $this->buildHierarchy(paycommEnabled: false);
        $bank = factory(Bank::class)->create();

        AuditOtherPaySchedule::create([
            'other_audit_payroll_category_id' => $category->id,
            'serial_number'                   => 1,
            'beneficiary_name'                => 'JANE DOE',
            'narration'                       => 'ALLOWANCE',
            'amount'                          => 20000,
            'account_number'                  => '9876543210',
            'bank_name'                       => $bank->name,
            'bank_code'                       => $bank->code,
            'bankable_type'                   => 'commercial',
            'bankable_id'                     => $bank->id,
        ]);

        (new GenerateAutopayOtherScheduleAction())->execute($category);

        $beneficiaryEntry = $category->autopaySchedules()->first();
        $this->assertEquals(20000, $beneficiaryEntry->amount);
    }

    /** @test */
    public function it_marks_category_as_autopay_generated()
    {
        ['domain' => $domain, 'category' => $category] = $this->buildHierarchy(paycommEnabled: true);
        $bank = factory(Bank::class)->create();

        AuditOtherPaySchedule::create([
            'other_audit_payroll_category_id' => $category->id,
            'serial_number'                   => 1,
            'beneficiary_name'                => 'TEST PERSON',
            'narration'                       => 'TEST',
            'amount'                          => 10000,
            'account_number'                  => '1111111111',
            'bank_name'                       => $bank->name,
            'bank_code'                       => $bank->code,
            'bankable_type'                   => 'commercial',
            'bankable_id'                     => $bank->id,
        ]);

        (new GenerateAutopayOtherScheduleAction())->execute($category);

        $category->refresh();
        $this->assertNotNull($category->autopay_generated);
    }

    /** @test */
    public function it_creates_microfinance_schedule_for_mfb_beneficiary()
    {
        ['domain' => $domain, 'category' => $category] = $this->buildHierarchy(paycommEnabled: false);
        $this->createCashPaymentMfb($domain);
        $mfb = $this->createRealMfb($domain);

        AuditOtherPaySchedule::create([
            'other_audit_payroll_category_id' => $category->id,
            'serial_number'                   => 1,
            'beneficiary_name'                => 'MFB PERSON',
            'narration'                       => 'SALARY',
            'amount'                          => 15000,
            'account_number'                  => '2222222222',
            'bank_name'                       => $mfb->name,
            'bank_code'                       => '999',
            'bankable_type'                   => 'micro_finance',
            'bankable_id'                     => $mfb->id,
        ]);

        (new GenerateAutopayOtherScheduleAction())->execute($category);

        $this->assertCount(1, MicrofinanceOtherSchedule::all());
    }

    /** @test */
    public function it_skips_cash_payment_mfb_beneficiaries()
    {
        ['domain' => $domain, 'category' => $category] = $this->buildHierarchy(paycommEnabled: false);
        $cashMfb = $this->createCashPaymentMfb($domain);

        AuditOtherPaySchedule::create([
            'other_audit_payroll_category_id' => $category->id,
            'serial_number'                   => 1,
            'beneficiary_name'                => 'CASH PERSON',
            'narration'                       => 'CASH',
            'amount'                          => 5000,
            'account_number'                  => '3333333333',
            'bank_name'                       => 'CASH PAYMENT',
            'bank_code'                       => '000',
            'bankable_type'                   => 'micro_finance',
            'bankable_id'                     => $cashMfb->id,
        ]);

        (new GenerateAutopayOtherScheduleAction())->execute($category);

        $this->assertCount(0, AutopayOtherSchedule::all());
        $this->assertCount(0, MicrofinanceOtherSchedule::all());
    }

    // ── helpers ────────────────────────────────────────────────────────────

    private function buildHierarchy(bool $paycommEnabled): array
    {
        $domain      = $this->createDomain();
        $user        = $this->createUser($domain);
        $paymentType = $this->createPaymentType('all');

        $payroll = $this->createAuditPayroll($domain, $user);

        if ($paycommEnabled) {
            $this->createPayComms($domain);
        } else {
            // We still need paycomms to be initialised; create them with 0 commission
            $bank = factory(Bank::class)->create();
            PayComm::create(['code' => 'PayComm I',  'name' => 'PayComm I',  'account_number' => '1111111111', 'commission' => 0, 'bankable_type' => 'commercial', 'bankable_id' => $bank->id, 'domain_id' => $domain->id]);
            PayComm::create(['code' => 'PayComm II', 'name' => 'PayComm II', 'account_number' => '2222222222', 'commission' => 0, 'bankable_type' => 'commercial', 'bankable_id' => $bank->id, 'domain_id' => $domain->id]);
        }

        $category = OtherAuditPayrollCategory::create([
            'audit_payroll_id' => $payroll->id,
            'payment_type_id'  => $paymentType->id,
            'payment_title'    => 'ALL STAFF',
            'paycomm_tenece'   => $paycommEnabled,
            'paycomm_fidelity' => $paycommEnabled,
        ]);

        return compact('domain', 'user', 'paymentType', 'payroll', 'category');
    }
}
