<?php

namespace App\Http\Controllers;

use App\Actions\SendDeductionConfirmation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

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
            'APIToken' => $token,
        ])->post($url, [
            'Narration' => $narration,
            'TransactionDate' => $date,
            'TotalAmount' => $total,
            'data' => $data,
        ]);

        return $response->json();
    }
}
