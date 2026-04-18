<?php

use App\Actions\InitiateSendDeductionConfirmation;
use App\Actions\SendDeductionConfirmation;
use App\Models\FidelityLoanSchedule;
use Illuminate\Support\Facades\Queue;

it('dispatches send deduction confirmation for each unsent schedule', function () {
    Queue::fake();

    createFidelitySchedule(['confirmation_sent' => null]);
    createFidelitySchedule(['confirmation_sent' => null]);
    createFidelitySchedule(['confirmation_sent' => now()]);  // already sent – must be skipped

    (new InitiateSendDeductionConfirmation())->handle();

    // lorisleiva/laravel-actions wraps dispatched jobs in a JobDecorator;
    // use the action's own assertPushed helper to match through the decorator.
    SendDeductionConfirmation::assertPushed(2);
});

it('does not dispatch anything when all schedules are already confirmed', function () {
    Queue::fake();

    createFidelitySchedule(['confirmation_sent' => now()]);
    createFidelitySchedule(['confirmation_sent' => now()]);

    (new InitiateSendDeductionConfirmation())->handle();

    SendDeductionConfirmation::assertNotPushed();
});

it('does nothing when there are no schedules', function () {
    Queue::fake();

    (new InitiateSendDeductionConfirmation())->handle();

    SendDeductionConfirmation::assertNotPushed();
});

// ── helpers ────────────────────────────────────────────────────────────

function createFidelitySchedule(array $overrides = []): FidelityLoanSchedule
{
    return FidelityLoanSchedule::create(array_merge([
        'audit_sub_mda_schedule_id' => 1,
        'payment_reference'         => 'REF' . uniqid(),
        'beneficiary_code'          => '1234567890',
        'beneficiary_name'          => 'Test Beneficiary',
        'account_number'            => '1234567890',
        'account_type'              => '10',
        'cbn_code'                  => '058',
        'is_cash_card'              => '0',
        'narration'                 => 'TEST NARRATION',
        'amount'                    => 5000,
        'email'                     => ' ',
        'currency'                  => 'NGN',
    ], $overrides));
}
