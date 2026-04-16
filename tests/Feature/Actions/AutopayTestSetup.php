<?php

namespace Tests\Feature\Actions;

use App\Models\AuditMdaSchedule;
use App\Models\AuditPayroll;
use App\Models\AuditPayrollCategory;
use App\Models\AuditSubMdaSchedule;
use App\Models\Bank;
use App\Models\BeneficiaryType;
use App\Models\Domain;
use App\Models\Mda;
use App\Models\MicroFinanceBank;
use App\Models\PayComm;
use App\Models\PaymentType;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Shared DB-setup helpers for GenerateAutoPayScheduleAction,
 * GenerateAutopayOtherScheduleAction and GenerateGroupAutopayScheduleAction
 * feature tests.
 */
trait AutopayTestSetup
{
    // ── Top-level hierarchy ────────────────────────────────────────────────

    protected function createDomain(): Domain
    {
        return Domain::create(['id' => 'test-domain', 'name' => 'Test Domain']);
    }

    protected function createUser(Domain $domain): User
    {
        return factory(User::class)->create(['domain_id' => $domain->id]);
    }

    protected function createPaymentType(string $id = 'sal', string $name = 'Salary'): PaymentType
    {
        // PaymentType has no $guarded/$fillable override; use DB insert to bypass guard.
        DB::table('payment_types')->insert(['id' => $id, 'name' => $name]);

        return PaymentType::find($id);
    }

    protected function createBeneficiaryType(Domain $domain): BeneficiaryType
    {
        return BeneficiaryType::create([
            'id'        => 'bt-test',
            'name'      => 'Staff',
            'domain_id' => $domain->id,
        ]);
    }

    protected function createMda(BeneficiaryType $beneficiaryType): Mda
    {
        return Mda::create([
            'code'                => 'MDA01',
            'name'                => 'Test MDA',
            'beneficiary_type_id' => $beneficiaryType->id,
        ]);
    }

    protected function createAuditPayroll(Domain $domain, User $user): AuditPayroll
    {
        return AuditPayroll::create([
            'month'      => 3,
            'month_name' => 'March',
            'year'       => 2024,
            'user_id'    => $user->id,
            'domain_id'  => $domain->id,
            'timestamp'  => '2024-03-01 00:00:00',
        ]);
    }

    protected function createAuditPayrollCategory(
        AuditPayroll $payroll,
        PaymentType $paymentType
    ): AuditPayrollCategory {
        return AuditPayrollCategory::create([
            'payment_type_id' => $paymentType->id,
            'payment_title'   => 'SALARY',
            'staff_type'      => 'staff',
            'audit_payroll_id' => $payroll->id,
        ]);
    }

    protected function createAuditMdaSchedule(
        AuditPayrollCategory $category,
        Mda $mda
    ): AuditMdaSchedule {
        return AuditMdaSchedule::create([
            'audit_payroll_category_id' => $category->id,
            'mda_id'                    => $mda->id,
            'mda_name'                  => $mda->name,
        ]);
    }

    protected function createAuditSubMdaSchedule(AuditMdaSchedule $mdaSchedule): AuditSubMdaSchedule
    {
        return AuditSubMdaSchedule::create([
            'audit_mda_schedule_id' => $mdaSchedule->id,
            'sub_mda_name'          => 'TEST SUB MDA',
        ]);
    }

    // ── PayComm / Bank helpers ─────────────────────────────────────────────

    /**
     * Creates PayComm I (50 NGN), PayComm II (100 NGN), and Fidelity Loan Collection
     * paycomms for the given domain, each backed by a Bank.
     *
     * @return array{payCommI: PayComm, payCommII: PayComm, fidelityComm: PayComm}
     */
    protected function createPayComms(Domain $domain): array
    {
        $bankI       = factory(Bank::class)->create();
        $bankII      = factory(Bank::class)->create();
        $bankFidelity = factory(Bank::class)->create();

        $payCommI = PayComm::create([
            'code'           => 'PayComm I',
            'name'           => 'PayComm I',
            'account_number' => '1111111111',
            'commission'     => 50,          // 50 NGN (setter × 100 → 5000 in DB)
            'bankable_type'  => 'commercial',
            'bankable_id'    => $bankI->id,
            'domain_id'      => $domain->id,
        ]);

        $payCommII = PayComm::create([
            'code'           => 'PayComm II',
            'name'           => 'PayComm II',
            'account_number' => '2222222222',
            'commission'     => 100,         // 100 NGN
            'bankable_type'  => 'commercial',
            'bankable_id'    => $bankII->id,
            'domain_id'      => $domain->id,
        ]);

        $fidelityComm = PayComm::create([
            'code'           => 'Fidelity Loan Collection',
            'name'           => 'Fidelity Loan',
            'account_number' => '3333333333',
            'commission'     => 0,
            'bankable_type'  => 'commercial',
            'bankable_id'    => $bankFidelity->id,
            'domain_id'      => $domain->id,
        ]);

        return compact('payCommI', 'payCommII', 'fidelityComm');
    }

    /**
     * Creates the mandatory "CASH PAYMENT" MicroFinanceBank that the generate actions
     * look up and skip when building MFB schedules.
     */
    protected function createCashPaymentMfb(Domain $domain): MicroFinanceBank
    {
        return MicroFinanceBank::create([
            'name'           => 'CASH PAYMENT',
            'account_number' => '0000000000',
            'bank_id'        => factory(Bank::class)->create()->id,
            'domain_id'      => $domain->id,
        ]);
    }

    /**
     * Creates a real MFB (not CASH PAYMENT) backed by a Bank with a known code.
     */
    protected function createRealMfb(Domain $domain, string $name = 'TEST MFB'): MicroFinanceBank
    {
        return MicroFinanceBank::create([
            'name'           => $name,
            'account_number' => '4444444444',
            'bank_id'        => factory(Bank::class)->create(['code' => '999'])->id,
            'domain_id'      => $domain->id,
        ]);
    }
}
