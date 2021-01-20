<?php

namespace App\Http\Controllers\Api;

use App\Models\BankDetail;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\AuditPaySchedule;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SalaryHistoryController extends Controller
{
    public function index(Request $request)
    {
        $bvn = Str::of($request->bvn)->trim();

        $this->logRequest(Str::upper($bvn));

        if ($bvn->length() !== 11) {
            return response()->json([
                'hasData'      => false,
                'requestDate'  => now()->format('d-m-Y H:i:s+0000'),
                'responseCode' => '07',
                'responseMsg'  => 'INVALID BVN',
                'data'         => [],
            ])->setStatusCode(Response::HTTP_NOT_FOUND);
        }

        $bank = BankDetail::query()
                          ->where('bank_verification_number', $bvn)
                          ->first();

        if (! $bank) {
            return response()->json([
                'hasData'      => false,
                'requestDate'  => now()->format('d-m-Y H:i:s+0000'),
                'responseCode' => '06',
                'responseMsg'  => 'BVN NOT FOUND',
                'data'         => [],
            ])->setStatusCode(Response::HTTP_NOT_FOUND);
        }

        $beneficiary = $bank->beneficiary;
        $domain = $beneficiary->domain;
        $staff_id = $beneficiary->verification_number;

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
                'responseCode' => '05',
                'responseMsg'  => 'NO SALARY DATA',
                'data'         => [],
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
            'salaryPaymentDetails' => $schedules->map(fn($schedule) => [
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
            "hasData"      => false,
            "responseDate" => now(),
            "requestDate"  => now(),
            "responseCode" => "00",
            "responseMsg"  => "INVALID REQUEST",
            "data"         => [
                "status" => "false",
            ],
        ])->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
