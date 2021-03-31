<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Actions\SendDeductionConfirmation;
use function config;

class DeductionConfirmationController extends Controller
{

    public function send(Request $request)
    {
        $account = $request->account;
        $narration = $request->narration;
        $amount = $request->amount;
        $date = $request->date;

        $url = config('fidelity.url');
        $user = config('fidelity.user');
        $token = config('fidelity.token');

        $response = Http::withHeaders([
            'APIUser' => $user,
            'APIToken' => $token
        ])->post($url, [
            'LoanAccount' => $account,
            'Narration' => $narration,
            'TransactionDate' => $date,
            'Amount' => $amount
        ]);

        return $response->json();
    }
}
