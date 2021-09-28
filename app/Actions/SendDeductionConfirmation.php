<?php

namespace App\Actions;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use App\Models\FidelityLoanSchedule;
use Illuminate\Support\Facades\Cache;
use App\Models\FidelityLoanDeduction;
use Lorisleiva\Actions\Concerns\AsAction;
use function collect;

class SendDeductionConfirmation
{
    use AsAction;

    public function handle(FidelityLoanSchedule $schedule)
    {
        $tokenUrl = config('fidelity.generate_token_url');
        $clientId = config('fidelity.client_id');
        $clientSecret = config('fidelity.client_secret');

        //Get Fidelity Loan Access Token and cache the token for 3000 seconds (50 mins)
        $token = Cache::remember(
            'fidelity_loan_token',
            3000,
            fn () => Http::post($tokenUrl, [
            'ClientID' => $clientId,
            'ClientSecret' => $clientSecret,
            ])->json()['access_token']
        );

        $bulkUrl = config('fidelity.confirm_deduction_url_bulk');

        $narration = $schedule->narration;

        $timestamp = Str::afterLast($narration, '|');
        $date = Carbon::createFromTimestamp($timestamp)
                      ->setDay(25)
                      ->setTime(23, 00)
                      ->format('Y-m-d\TH:i:s+000Z');
        $total = $schedule->totalAmount();
        $data = $schedule->deductions->transform(fn (FidelityLoanDeduction $deduction) => [
            'LoanAccount' => $deduction->loan_account,
            'Amount' => $deduction->amount
        ]);

        $response = Http::withToken($token)
                        ->post($bulkUrl, [
                            'Narration' => $narration,
                            'TransactionDate' => $date,
                            'TotalAmount' => $total,
                            'data' => $data
                        ]);

        $payload = $response->json();

        $schedule->response_data = collect($payload);

        if ($response->successful() && $payload['Status'] === 'SUCCESS') {
            $schedule->confirmation_sent = now();
        }

        $schedule->save();
    }
}
