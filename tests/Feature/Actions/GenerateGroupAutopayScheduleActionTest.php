<?php

namespace Tests\Feature\Actions;

use App\Actions\GenerateGroupAutopayScheduleAction;
use App\Models\AuditPaySchedule;
use App\Models\AutopaySchedule;
use App\Models\Bank;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GenerateGroupAutopayScheduleActionTest extends TestCase
{
    use RefreshDatabase, AutopayTestSetup;

    /** @test */
    public function it_creates_autopay_schedules_for_commercial_bank_beneficiaries()
    {
        [
            'domain'          => $domain,
            'category'        => $category,
            'beneficiaryType' => $beneficiaryType,
            'subMda'          => $subMda,
        ] = $this->buildHierarchy();

        $bank = Bank::factory()->create(['code' => '058']);

        AuditPaySchedule::create([
            'audit_sub_mda_schedule_id' => $subMda->id,
            'verification_number'       => 'VN001',
            'beneficiary_name'          => 'JOHN DOE',
            'designation'               => 'Officer',
            'mda'                       => 'Test MDA',
            'department'                => 'Test Dept',
            'basic_pay'                 => 30000,
            'bank_name'                 => $bank->name,
            'account_number'            => '1234567890',
            'bank_code'                 => $bank->code,
            'total_allowance'           => 0,
            'total_deductions'          => 0,
            'total_dues'                => 0,
            'total_dues_deductions'     => 0,
            'gross_pay'                 => 50000,
            'net_pay'                   => 50000,
            'allowances'                => [],
            'deductions'                => [],
            'dues'                      => [],
            'month'                     => '2024-03-01 00:00:00',
            'bankable_type'             => 'commercial',
            'bankable_id'               => $bank->id,
        ]);

        (new GenerateGroupAutopayScheduleAction())->execute($domain, $category, $beneficiaryType);

        // Beneficiary + PayComm I + PayComm II
        $this->assertCount(3, AutopaySchedule::all());

        $beneficiaryEntry = $subMda->autopaySchedules()->orderBy('id')->first();
        // net_pay (50000) − paycomm_i (50) − paycomm_ii (100) = 49850
        $this->assertEquals(49850, $beneficiaryEntry->amount);
    }

    /** @test */
    public function it_marks_all_touched_sub_mdas_as_autopay_generated()
    {
        [
            'domain'          => $domain,
            'category'        => $category,
            'beneficiaryType' => $beneficiaryType,
            'subMda'          => $subMda,
        ] = $this->buildHierarchy();

        $bank = Bank::factory()->create();

        AuditPaySchedule::create([
            'audit_sub_mda_schedule_id' => $subMda->id,
            'verification_number'       => 'VN002',
            'beneficiary_name'          => 'TEST PERSON',
            'designation'               => 'Officer',
            'mda'                       => 'Test MDA',
            'department'                => 'Test Dept',
            'basic_pay'                 => 20000,
            'bank_name'                 => $bank->name,
            'account_number'            => '9999999999',
            'bank_code'                 => $bank->code,
            'total_allowance'           => 0,
            'total_deductions'          => 0,
            'total_dues'                => 0,
            'total_dues_deductions'     => 0,
            'gross_pay'                 => 40000,
            'net_pay'                   => 40000,
            'allowances'                => [],
            'deductions'                => [],
            'dues'                      => [],
            'month'                     => '2024-03-01 00:00:00',
            'bankable_type'             => 'commercial',
            'bankable_id'               => $bank->id,
        ]);

        (new GenerateGroupAutopayScheduleAction())->execute($domain, $category, $beneficiaryType);

        $subMda->refresh();
        $this->assertNotNull($subMda->autopay_generated);
    }

    /** @test */
    public function it_only_processes_schedules_matching_the_given_beneficiary_type()
    {
        [
            'domain'          => $domain,
            'category'        => $category,
            'beneficiaryType' => $targetType,
            'mdaSchedule'     => $mdaSchedule,
        ] = $this->buildHierarchy();

        // A second sub_mda under a different MDA whose beneficiary type differs
        $otherType       = $this->createBeneficiaryType2($domain);
        $otherMda        = $this->createMda($otherType);
        $otherMdaSchedule = $this->createAuditMdaSchedule($category, $otherMda);
        $otherSubMda     = $this->createAuditSubMdaSchedule($otherMdaSchedule);

        $bank = Bank::factory()->create();

        // Schedule under the target beneficiary type — unique account not used by any paycomm
        AuditPaySchedule::create([
            'audit_sub_mda_schedule_id' => $this->subMdaFor($mdaSchedule)->id,
            'verification_number'       => 'VN003',
            'beneficiary_name'          => 'TARGET PERSON',
            'designation'               => 'Officer',
            'mda'                       => 'Test MDA',
            'department'                => 'Test Dept',
            'basic_pay'                 => 10000,
            'bank_name'                 => $bank->name,
            'account_number'            => '5555555555',
            'bank_code'                 => $bank->code,
            'total_allowance'           => 0,
            'total_deductions'          => 0,
            'total_dues'                => 0,
            'total_dues_deductions'     => 0,
            'gross_pay'                 => 20000,
            'net_pay'                   => 20000,
            'allowances'                => [],
            'deductions'                => [],
            'dues'                      => [],
            'month'                     => '2024-03-01 00:00:00',
            'bankable_type'             => 'commercial',
            'bankable_id'               => $bank->id,
        ]);

        // Schedule under the OTHER beneficiary type (should be ignored)
        AuditPaySchedule::create([
            'audit_sub_mda_schedule_id' => $otherSubMda->id,
            'verification_number'       => 'VN004',
            'beneficiary_name'          => 'OTHER PERSON',
            'designation'               => 'Officer',
            'mda'                       => 'Other MDA',
            'department'                => 'Other Dept',
            'basic_pay'                 => 10000,
            'bank_name'                 => $bank->name,
            'account_number'            => '9876543210',
            'bank_code'                 => $bank->code,
            'total_allowance'           => 0,
            'total_deductions'          => 0,
            'total_dues'                => 0,
            'total_dues_deductions'     => 0,
            'gross_pay'                 => 20000,
            'net_pay'                   => 20000,
            'allowances'                => [],
            'deductions'                => [],
            'dues'                      => [],
            'month'                     => '2024-03-01 00:00:00',
            'bankable_type'             => 'commercial',
            'bankable_id'               => $bank->id,
        ]);

        (new GenerateGroupAutopayScheduleAction())->execute($domain, $category, $targetType);

        // Only the one beneficiary matching targetType + PayComm I + PayComm II = 3
        $this->assertCount(3, AutopaySchedule::all());
        $accounts = AutopaySchedule::pluck('account_number')->toArray();
        $this->assertContains('5555555555', $accounts);
        $this->assertNotContains('9876543210', $accounts);
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

        $this->createPayComms($domain);
        $this->createCashPaymentMfb($domain);

        return compact('domain', 'user', 'paymentType', 'beneficiaryType', 'mda', 'payroll', 'category', 'mdaSchedule', 'subMda');
    }

    private function createBeneficiaryType2(\App\Models\Domain $domain): \App\Models\BeneficiaryType
    {
        return \App\Models\BeneficiaryType::create([
            'id'        => 'bt-other',
            'name'      => 'Other Type',
            'domain_id' => $domain->id,
        ]);
    }

    private function subMdaFor(\App\Models\AuditMdaSchedule $mdaSchedule): \App\Models\AuditSubMdaSchedule
    {
        return $mdaSchedule->auditSubMdaSchedules()->firstOrFail();
    }
}
