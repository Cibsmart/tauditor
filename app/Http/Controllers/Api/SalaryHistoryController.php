<?php

namespace App\Http\Controllers\Api;

use App\Models\Beneficiary;
use App\Http\Controllers\Controller;
use App\Models\AuditPaySchedule;
use App\Models\BankDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use function json_encode;
use Symfony\Component\HttpFoundation\Response;

class SalaryHistoryController extends Controller
{
    public function show(Request $request)
    {
        $bvn = Str::of($request->bvn)->trim();
        $auth = $request->authorizationCode;
        $empty = (object) [];

        $this->logRequest(['bvn' => Str::upper($bvn), 'authorization_code' => $auth]);

        if (! $auth) {
            return response()->json([
                'hasData'      => false,
                'requestDate'  => now()->format('d-m-Y H:i:s+0000'),
                'responseCode' => '07',
                'responseMsg'  => 'UNAUTHORIZED',
                'data'         => $empty,
            ])->setStatusCode(Response::HTTP_FORBIDDEN);
        }

        if ($bvn->length() !== 11) {
            return response()->json([
                'hasData'      => false,
                'requestDate'  => now()->format('d-m-Y H:i:s+0000'),
                'responseCode' => '07',
                'responseMsg'  => 'INVALID BVN',
                'data'         => $empty,
            ])->setStatusCode(Response::HTTP_NOT_FOUND);
        }

        $bank = BankDetail::query()
                          ->where('bank_verification_number', $bvn)
                          ->first();

        if (! $bank) {
            return response()->json([
                'hasData'      => false,
                'requestDate'  => now()->format('d-m-Y H:i:s+0000'),
                'responseCode' => '07',
                'responseMsg'  => 'BVN NOT FOUND',
                'data'         => $empty,
            ])->setStatusCode(Response::HTTP_NOT_FOUND);
        }

        $beneficiary = $bank->beneficiary;
        $domain = $beneficiary->domain;
        $staff_id = $beneficiary->verification_number;

        $count = Beneficiary::query()->where('verification_number', $staff_id)->count();

        if ($count > 1) {
            return response()->json([
                'hasData'      => false,
                'requestDate'  => now()->format('d-m-Y H:i:s+0000'),
                'responseCode' => '07',
                'responseMsg'  => 'INVALID BENEFICIARY RECORD',
                'data'         => $empty,
            ])->setStatusCode(Response::HTTP_NOT_FOUND);
        }

        $schedules = AuditPaySchedule::allSchedules()
                                     ->orderByMonth()
                                     ->where('verification_number', $staff_id)
                                     ->where('domain_id', $domain->id)
                                     ->limit(3)
                                     ->get();

        if ($schedules->isEmpty()) {
            return response()->json([
                'hasData'      => false,
                'requestDate'  => now()->format('d-m-Y H:i:s+0000'),
                'responseCode' => '07',
                'responseMsg'  => 'NO SALARY DATA',
                'data'         => $empty,
            ])->setStatusCode(Response::HTTP_NOT_FOUND);
        }

        $data = [
            'bvn'                  => $bank->bvn,
            'customerId'           => $beneficiary->verification_number,
            'email'                => $beneficiary->email ?? '',
            'accountNumber'        => $bank->account_number,
            'dob'                  => $beneficiary->dob,
            'address'              => $beneficiary->address ?? '',
            'phoneNumber'          => $beneficiary->phone_number ?? '',
            'companyName'          => $domain->name,
            'firstPaymentDate'     => $schedules->last()->payment_date,
            'lastPaymentDate'      => $schedules->first()->payment_date,
            'salaryCount'          => $schedules->count(),
            'salaryPaymentDetails' => $schedules->map(fn ($schedule) => [
                'paymentDate'   => $schedule->payment_date,
                'amount'        => $schedule->net_pay,
                'accountNumber' => $schedule->account_number,
            ]),
        ];

        return response()->json([
            'hasData'      => true,
            'requestDate'  => now()->format('d-m-Y H:i:s+0000'),
            'responseCode' => '00',
            'responseMsg'  => 'SUCCESS',
            'data'         => $data,
        ])->setStatusCode(Response::HTTP_FOUND);
    }

    protected function logRequest($bvn)
    {
        $log = [
            'endpoint' => 'salary_history',
            'data'     => collect(['bvn' => $bvn]),
            'ip'       => request()->ip(),
        ];

        Auth::user()->requests()->create($log);
    }

    public function invalid()
    {
        return response()->json([
            'hasData'      => false,
            'responseDate' => now(),
            'requestDate'  => now(),
            'responseCode' => '00',
            'responseMsg'  => 'INVALID REQUEST',
            'data'         => [
                'status' => 'false',
            ],
        ])->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
