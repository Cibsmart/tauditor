<?php

namespace App\Actions;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Lorisleiva\Actions\Concerns\AsAction;

class SendDeductionConfirmation
{
    use AsAction;

    public function handle()
    {
        $tokenUrl = config('fidelity.generate_token_url');
        $clientId = config('fidelity.client_id');
        $clientSecret = config('fidelity.client_secret');

        //Get Fidelity Loan Access Token and cache the token for 3600 seconds
        $token = Cache::remember(
            'fidelity_loan_token',
            3600,
            fn () => Http::post($tokenUrl, [
            'ClientID' => $clientId,
            'ClientSecret' => $clientSecret,
            ])->json()['access_token']
        );

        dd($token);

        //TODO: Implement Send Deduction Confirmation
        //Get and Prepare Payload to be send in bulk to Fidelity
        $bulkUrl = config('fidelity.confirm_deduction_url_bulk');

        $narration = '';
        $date = '';
        $total = '';
        $data = '';


        $response = Http::withToken($token)
                        ->post($bulkUrl, [
                            'Narration' => $narration,
                            'TransactionDate' => $date,
                            'TotalAmount' => $total,
                            'data' => $data
                        ]);
    }
}
