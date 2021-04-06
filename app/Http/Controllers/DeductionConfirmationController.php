<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Actions\SendDeductionConfirmation;
use function config;
use function array_sum;

class DeductionConfirmationController extends Controller
{

    public function send(Request $request)
    {

        $narration = $request->narration;
        $date = $request->date;
        $data = collect($request->data);
        $total = $data->sum('amount');

        $url = config('fidelity.url');
        $user = config('fidelity.user');
        $token = config('fidelity.token');

        $response = Http::withHeaders([
            'APIUser' => $user,
            'APIToken' => $token
        ])->post($url, [
            'Narration' => $narration,
            'TransactionDate' => $date,
            'TotalAmount' => $total,
            'data' => $data
        ]);

        return $response->json();
    }
}
