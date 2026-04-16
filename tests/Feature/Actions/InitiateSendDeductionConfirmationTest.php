<?php

namespace Tests\Feature\Actions;

use App\Actions\InitiateSendDeductionConfirmation;
use App\Actions\SendDeductionConfirmation;
use App\Models\FidelityLoanSchedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class InitiateSendDeductionConfirmationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_dispatches_send_deduction_confirmation_for_each_unsent_schedule()
    {
        Queue::fake();

        $this->createSchedule(['confirmation_sent' => null]);
        $this->createSchedule(['confirmation_sent' => null]);
        $this->createSchedule(['confirmation_sent' => now()]);  // already sent – must be skipped

        (new InitiateSendDeductionConfirmation())->handle();

        // lorisleiva/laravel-actions wraps dispatched jobs in a JobDecorator;
        // use the action's own assertPushed helper to match through the decorator.
        SendDeductionConfirmation::assertPushed(2);
    }

    /** @test */
    public function it_does_not_dispatch_anything_when_all_schedules_are_already_confirmed()
    {
        Queue::fake();

        $this->createSchedule(['confirmation_sent' => now()]);
        $this->createSchedule(['confirmation_sent' => now()]);

        (new InitiateSendDeductionConfirmation())->handle();

        SendDeductionConfirmation::assertNotPushed();
    }

    /** @test */
    public function it_does_nothing_when_there_are_no_schedules()
    {
        Queue::fake();

        (new InitiateSendDeductionConfirmation())->handle();

        SendDeductionConfirmation::assertNotPushed();
    }

    // ── helpers ────────────────────────────────────────────────────────────

    private function createSchedule(array $overrides = []): FidelityLoanSchedule
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
}
