<?php

use App\Actions\SendDeductionConfirmation;
use App\Models\FidelityLoanSchedule;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    Config::set('fidelity.generate_token_url', 'https://fidelity.test/token');
    Config::set('fidelity.client_id', 'test-client-id');
    Config::set('fidelity.client_secret', 'test-client-secret');
    Config::set('fidelity.confirm_deduction_url_bulk', 'https://fidelity.test/confirm');
});

it('sets confirmation sent when response code is 00', function () {
    Http::fake([
        'https://fidelity.test/token'   => Http::response(['access_token' => 'test-token'], 200),
        'https://fidelity.test/confirm' => Http::response(['responseCode' => '00', 'message' => 'Success'], 200),
    ]);

    $schedule = createScheduleWithDeduction();

    (new SendDeductionConfirmation())->handle($schedule);

    expect($schedule->refresh()->confirmation_sent)->not->toBeNull();
});

it('does not set confirmation sent when response code is not 00', function () {
    Http::fake([
        'https://fidelity.test/token'   => Http::response(['access_token' => 'test-token'], 200),
        'https://fidelity.test/confirm' => Http::response(['responseCode' => '99', 'message' => 'Failure'], 200),
    ]);

    $schedule = createScheduleWithDeduction();

    (new SendDeductionConfirmation())->handle($schedule);

    expect($schedule->refresh()->confirmation_sent)->toBeNull();
});

it('stores the api response on the schedule', function () {
    Http::fake([
        'https://fidelity.test/token'   => Http::response(['access_token' => 'test-token'], 200),
        'https://fidelity.test/confirm' => Http::response(['responseCode' => '00', 'status' => 'OK'], 200),
    ]);

    $schedule = createScheduleWithDeduction();

    (new SendDeductionConfirmation())->handle($schedule);

    expect($schedule->refresh()->response_data)->not->toBeEmpty();
});

it('does not set confirmation sent on a failed http response', function () {
    Http::fake([
        'https://fidelity.test/token'   => Http::response(['access_token' => 'test-token'], 200),
        'https://fidelity.test/confirm' => Http::response(['error' => 'Internal Server Error'], 500),
    ]);

    $schedule = createScheduleWithDeduction();

    (new SendDeductionConfirmation())->handle($schedule);

    expect($schedule->refresh()->confirmation_sent)->toBeNull();
});

it('caches the access token to avoid repeated token requests', function () {
    Http::fake([
        'https://fidelity.test/token'   => Http::response(['access_token' => 'cached-token'], 200),
        'https://fidelity.test/confirm' => Http::response(['responseCode' => '00'], 200),
    ]);

    $schedule1 = createScheduleWithDeduction();
    $schedule2 = createScheduleWithDeduction();

    (new SendDeductionConfirmation())->handle($schedule1);
    (new SendDeductionConfirmation())->handle($schedule2);

    // Token endpoint should only have been called once; the second call uses the cache.
    Http::assertSentCount(3); // 1 token request + 2 confirm requests
});

// ── helpers ────────────────────────────────────────────────────────────

/**
 * Create a minimal FidelityLoanSchedule.
 * No FidelityLoanDeduction records are inserted here because the deduction
 * table has foreign-key constraints (loan_mandates, audit_sub_mda_schedules)
 * that would require the full payroll hierarchy. The action under test still
 * posts to the Fidelity API with an empty Data array, so all HTTP/caching
 * behaviour is fully exercised.
 */
function createScheduleWithDeduction(): FidelityLoanSchedule
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
