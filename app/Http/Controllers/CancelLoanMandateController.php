<?php

namespace App\Http\Controllers;

use App\Models\Beneficiary;
use App\Models\LoanMandate;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use function now;
use function response;
use Symfony\Component\HttpFoundation\Response;
use function uniqid;

class CancelLoanMandateController extends Controller
{
    //
    public function create(Request $request)
    {
        $request_date = now();

        $staff_id = $request->customerId;
        $auth = $request->authorisationCode;
        $reference = $request->mandateReference;

        if (! $auth) {
            return response()->json([
                'hasData'      => false,
                'responseDate' => now()->format('d-m-Y H:i:s+0000'),
                'requestDate'  => $request_date->format('d-m-Y H:i:s+0000'),
                'responseCode' => '07',
                'responseMsg'  => 'UNAUTHORIZED',
                'data'         => [],
            ])->setStatusCode(Response::HTTP_FORBIDDEN);
        }

        $beneficiary = Beneficiary::query()
                                  ->where('verification_number', $staff_id)
                                  ->first();

        if (! $beneficiary) {
            return response()->json([
                'hasData'      => false,
                'responseDate' => now()->format('d-m-Y H:i:s+0000'),
                'requestDate'  => $request_date->format('d-m-Y H:i:s+0000'),
                'responseCode' => '07',
                'responseMsg'  => 'INVALID CUSTOMER ID',
                'data'         => [],
            ])->setStatusCode(Response::HTTP_FORBIDDEN);
        }

        $mandate = LoanMandate::query()
                              ->where('reference', $reference)
                              ->first();

        if (! $mandate) {
            return response()->json([
                'hasData'      => false,
                'responseDate' => now()->format('d-m-Y H:i:s+0000'),
                'requestDate'  => $request_date->format('d-m-Y H:i:s+0000'),
                'responseCode' => '07',
                'responseMsg'  => 'INVALID MANDATE REFERENCE',
                'data'         => [],
            ])->setStatusCode(Response::HTTP_FORBIDDEN);
        }

        if ($mandate->staff_id !== $beneficiary->verification_number) {
            return response()->json([
                'hasData'      => false,
                'responseDate' => now()->format('d-m-Y H:i:s+0000'),
                'requestDate'  => $request_date->format('d-m-Y H:i:s+0000'),
                'responseCode' => '07',
                'responseMsg'  => 'MANDATE REFERENCE MISMATCH',
                'data'         => [],
            ])->setStatusCode(Response::HTTP_FORBIDDEN);
        }

        $y = $mandate->cancel();

        return response()->json([
            'hasData'      => true,
            'responseDate'  => now()->format('d-m-Y H:i:s+0000'),
            'requestDate'  => $request_date->format('d-m-Y H:i:s+0000'),
            'responseCode' => '00',
            'responseMsg'  => 'SUCCESS',
            'data'         => [
                'customerId' => $beneficiary->verification_number,
                'mandateReference' => $mandate->reference,
                'status' => 'false',
            ],
        ])->setStatusCode(Response::HTTP_FOUND);
    }
}
