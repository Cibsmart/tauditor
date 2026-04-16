<?php

namespace Tests\Feature\Actions;

use App\Actions\SendDeductionConfirmation;
use App\Models\FidelityLoanSchedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SendDeductionConfirmationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Config::set('fidelity.generate_token_url', 'https://fidelity.test/token');
        Config::set('fidelity.client_id', 'test-client-id');
        Config::set('fidelity.client_secret', 'test-client-secret');
        Config::set('fidelity.confirm_deduction_url_bulk', 'https://fidelity.test/confirm');
    }

    /** @test */
    public function it_sets_confirmation_sent_when_response_code_is_00()
    {
        Http::fake([
            'https://fidelity.test/token'   => Http::response(['access_token' => 'test-token'], 200),
            'https://fidelity.test/confirm' => Http::response(['responseCode' => '00', 'message' => 'Success'], 200),
        ]);

        $schedule = $this->createScheduleWithDeduction();

        (new SendDeductionConfirmation())->handle($schedule);

        $schedule->refresh();
        $this->assertNotNull($schedule->confirmation_sent);
    }

    /** @test */
    public function it_does_not_set_confirmation_sent_when_response_code_is_not_00()
    {
        Http::fake([
            'https://fidelity.test/token'   => Http::response(['access_token' => 'test-token'], 200),
            'https://fidelity.test/confirm' => Http::response(['responseCode' => '99', 'message' => 'Failure'], 200),
        ]);

        $schedule = $this->createScheduleWithDeduction();

        (new SendDeductionConfirmation())->handle($schedule);

        $schedule->refresh();
        $this->assertNull($schedule->confirmation_sent);
    }

    /** @test */
    public function it_stores_the_api_response_on_the_schedule()
    {
        Http::fake([
            'https://fidelity.test/token'   => Http::response(['access_token' => 'test-token'], 200),
            'https://fidelity.test/confirm' => Http::response(['responseCode' => '00', 'status' => 'OK'], 200),
        ]);

        $schedule = $this->createScheduleWithDeduction();

        (new SendDeductionConfirmation())->handle($schedule);

        $schedule->refresh();
        $this->assertNotEmpty($schedule->response_data);
    }

    /** @test */
    public function it_does_not_set_confirmation_sent_on_a_failed_http_response()
    {
        Http::fake([
            'https://fidelity.test/token'   => Http::response(['access_token' => 'test-token'], 200),
            'https://fidelity.test/confirm' => Http::response(['error' => 'Internal Server Error'], 500),
        ]);

        $schedule = $this->createScheduleWithDeduction();

        (new SendDeductionConfirmation())->handle($schedule);

        $schedule->refresh();
        $this->assertNull($schedule->confirmation_sent);
    }

    /** @test */
    public function it_caches_the_access_token_to_avoid_repeated_token_requests()
    {
        Http::fake([
            'https://fidelity.test/token'   => Http::response(['access_token' => 'cached-token'], 200),
            'https://fidelity.test/confirm' => Http::response(['responseCode' => '00'], 200),
        ]);

        $schedule1 = $this->createScheduleWithDeduction();
        $schedule2 = $this->createScheduleWithDeduction();

        (new SendDeductionConfirmation())->handle($schedule1);
        (new SendDeductionConfirmation())->handle($schedule2);

        // Token endpoint should only have been called once; the second call uses the cache.
        Http::assertSentCount(3); // 1 token request + 2 confirm requests
    }

    // ── helpers ────────────────────────────────────────────────────────────

    /**
     * Create a minimal FidelityLoanSchedule.
     * No FidelityLoanDeduction records are inserted here because the deduction
     * table has foreign-key constraints (loan_mandates, audit_sub_mda_schedules)
     * that would require the full payroll hierarchy.  The action under test still
     * posts to the Fidelity API with an empty Data array, so all HTTP/caching
     * behaviour is fully exercised.
     */
    private function createScheduleWithDeduction(): FidelityLoanSchedule
    {
        return FidelityLoanSchedule::create([
            'audit_sub_mda_schedule_id' => 1,
            'payment_reference'         => 'REF' . uniqid(),
            'beneficiary_code'          => '1234567890',
            'beneficiary_name'          => 'TEST BENEFICIARY',
            'account_number'            => '1234567890',
            'account_type'              => '10',
            'cbn_code'                  => '070',
            'is_cash_card'              => '0',
            'narration'                 => 'TESTNARN',
            'amount'                    => 5000,
            'email'                     => ' ',
            'currency'                  => 'NGN',
        ]);
    }
}
