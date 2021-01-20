<?php

namespace App\Http\Controllers\Api;

use App\Models\Beneficiary;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use function now;
use function uniqid;
use function response;

class LoanMandateController extends Controller
{
    //
    public function create(Request $request)
    {
        $request_date = now();

        $staff_id = $request->customerId;
        $auth = $request->authorisationCode;
        $bvn = $request->bvn;

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

        if ($beneficiary->bvn() !== $bvn) {
            return response()->json([
                'hasData'      => false,
                'responseDate' => now()->format('d-m-Y H:i:s+0000'),
                'requestDate'  => $request_date->format('d-m-Y H:i:s+0000'),
                'responseCode' => '07',
                'responseMsg'  => 'BVN MISMATCH',
                'data'         => [],
            ])->setStatusCode(Response::HTTP_FORBIDDEN);
        }

        $attributes = [
            'reference' => Str::upper(uniqid()),
            'staff_id' => $beneficiary->verification_number,
            'bvn' => $beneficiary->bvn(),
            'account_number' => $request->accountNumber,
            'phone_number' => $request->phoneNumber,
            'currency' => $request->currency,
            'loan_amount' => $request->loanAmount,
            'collection_amount' => $request->collectionAmount,
            'total_collection_amount' => $request->totalCollectionAmount,
            'number_of_repayments' => $request->numberOfRepayments,
            'disbursement_date' => $request->dateOfDisbursement,
            'collection_date' => $request->dateOfCollection,
            'authorization_code' => $auth,
            'authorization_channel' => $request->authorisationChannel,
            'status' => 'A',
        ];

        $mandate = $beneficiary->mandate()->create($attributes);

        $bank = $beneficiary->bankDetail;

        return response()->json([
            'hasData'      => true,
            'responseDate'  => now()->format('d-m-Y H:i:s+0000'),
            'requestDate'  => $request_date->format('d-m-Y H:i:s+0000'),
            'responseCode' => '00',
            'responseMsg'  => 'SUCCESS',
            'data'         => [
                'authorisationCode' => $auth,
                'accountNumber' => $bank->account_number,
                'bankCode' => $bank->bankable->code,
                'amount' => $mandate->loan_amount,
                'customerId' => $beneficiary->verification_number,
                'mandateReference' => $mandate->reference,
            ],
        ])->setStatusCode(Response::HTTP_FOUND);
    }
}
