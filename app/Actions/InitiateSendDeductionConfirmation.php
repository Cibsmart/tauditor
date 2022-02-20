<?php

namespace App\Actions;

use App\Models\FidelityLoanSchedule;
use Illuminate\Console\Command;
use Lorisleiva\Actions\Concerns\AsAction;

class InitiateSendDeductionConfirmation
{
    use AsAction;

    public string $commandSignature = 'fidelity:send-deduction-confirmation';

    public string $commandDescription = 'Send Fidelity Loan Deduction Confirmation';

    public function handle()
    {
        $schedules = FidelityLoanSchedule::query()->whereNull('confirmation_sent')->get();

        foreach ($schedules as $schedule) {
            SendDeductionConfirmation::dispatch($schedule);
        }
    }

    public function asCommand(Command $command)
    {
        $this->handle();

        $command->info('Done');
    }
}
