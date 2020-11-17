<?php

namespace App\Http\Controllers\Api;

use App\Models\Domain;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\AuditPaySchedule;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Resources\LoanResourceCollection;
use function collect;
use function response;

class PaymentHistoryController extends Controller
{
    //
    public function index(Request $request)
    {
        $domain = $request->domain;
        $verification_number = $request->staff_id;

        $this->logRequest($domain, $verification_number);

        $domain = Domain::find(Str::of($domain)->trim());

        if (! $domain) {
            return response()->json([
                'is_staff_valid' => false,
                'data'           => [],
                'message'        => 'Invalid Domain',
            ])->setStatusCode(Response::HTTP_NOT_FOUND);
        }

        $schedules = AuditPaySchedule::allSchedules()
                                     ->orderByMonth()
                                     ->where('verification_number', Str::of($verification_number)->trim())
                                     ->where('domain_id', Str::of($domain->id)->trim())
                                     ->limit(3)
                                     ->get();

        if ($schedules->isEmpty()) {
            return response()->json([
                'is_staff_valid' => false,
                'data'           => [],
                'message'        => 'Staff ID Does not Exist',
            ])->setStatusCode(Response::HTTP_NOT_FOUND);
        }

        $mda_name = $schedules[0]->mda_name;
        $sub_name = $schedules[0]->sub_mda_name;

        $mda_name = $mda_name === $sub_name ? $mda_name : "{$mda_name} ({$sub_name})";

        $data = [
            'staff_id'    => $schedules[0]->verification_number,
            'staff_name'  => $schedules[0]->beneficiary_name,
            'staff_cadre' => $schedules[0]->beneficiary_cadre,
            'staff_mda'   => $mda_name,
            'schedules'   => $schedules->map(fn($schedule) => [
                'net_pay'        => number_format($schedule->net_pay, 2, '.', ','),
                'month'          => "{$schedule->month_name} {$schedule->year}",
                'bank_name'      => $schedule->bank_name,
                'account_number' => $schedule->account_number,
            ]),
        ];

        return new LoanResourceCollection($data);
    }

    public function invalid()
    {
        return response()->json([
            'is_staff_valid' => false,
            'data'           => [],
            'message'        => 'Invalid Request',
        ])->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    protected function logRequest($domain, $verification_number)
    {
        $log = [
            'endpoint' => 'payment_history',
            'data'     => collect(['domain' => $domain, 'verification_number' => $verification_number]),
            'ip'       => request()->ip(),
        ];

        Auth::user()->requests()->create($log);
    }
}
