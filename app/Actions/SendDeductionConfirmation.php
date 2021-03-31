<?php


namespace App\Actions;


use Illuminate\Support\Facades\Http;

class SendDeductionConfirmation
{
    public function execute($account, $narration, $amount, $date)
    {
        $url = config('fidelity.url');
        $user = config('fidelity.user');
        $token = config('fidelity.token');

        return [$account, $narration, $amount, $date, $url, $user, $token];

        $response = Http::withHeaders([
            'APIUser' => $user,
            'APIToken' => $token
        ])->post($url, [
            'LoanAccount' => $account,
            'Narration' => $narration,
            'TransactionDate' => $date,
            'Amount' => $amount
        ]);

        dd($response);
    }
}
