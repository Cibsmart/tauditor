<?php

namespace App\Http\Controllers;

use App\Domain;
use App\AuditPaySchedule;
use Illuminate\Support\Str;
use App\Http\Resources\LoanResource;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Resources\LoanResourceCollection;
use function response;
use function number_format;

class LoanDataController extends Controller
{
    //
    public function index($domain, $verification_number, $account_number)
    {
        $domain = Domain::find(Str::of($domain)->trim());

        if (! $domain) {
            return response()->json([
                'is_staff_valid'  => false,
                'data'  => [],
                'message' => 'Invalid Domain',
            ])->setStatusCode(Response::HTTP_NOT_FOUND);
        }

        $beneficiary = AuditPaySchedule::query()
                                       ->where('verification_number', Str::of($verification_number)->trim())
                                       ->orderBy('month', 'desc')
                                       ->first();

        if (! $beneficiary) {
            return response()->json([
                'is_staff_valid'  => false,
                'data'  => [],
                'message' => 'Staff ID Does not Exist',
            ])->setStatusCode(Response::HTTP_NOT_FOUND);
        }


        $account = AuditPaySchedule::query()
                                   ->where('account_number', Str::of($account_number)->trim())
                                   ->first();

        if (! $account) {
            return response()->json([
                'is_staff_valid'  => false,
                'data'  => [],
                'message' => 'Account Number Does not Exist',
            ])->setStatusCode(Response::HTTP_NOT_FOUND);
        }


        $schedules = AuditPaySchedule::allSchedules()
                                     ->orderByMonth()
                                     ->where('verification_number', Str::of($verification_number)->trim())
                                     ->where('account_number', Str::of($account_number)->trim())
                                     ->where('domain_id', Str::of($domain->id)->trim())
                                     ->limit(3)
                                     ->get();

        if ($schedules->isEmpty()) {
            return response()->json([
                'is_staff_valid'  => false,
                'data'  => [],
                'message' => 'No Matching Record',
            ])->setStatusCode(Response::HTTP_NOT_FOUND);
        }

        $mda_name = $schedules[0]->mda_name;
        $sub_name = $schedules[0]->sub_mda_name;

        $mda_name = $mda_name === $sub_name ? $mda_name : "{$mda_name} ({$sub_name})";

        $data = [
            'staff_id' => $schedules[0]->verification_number,
            'staff_name' => $schedules[0]->beneficiary_name,
            'staff_cadre' => $schedules[0]->beneficiary_cadre,
            'staff_mda' => $mda_name,
            'bank_name' => $schedules[0]->bank_name,
            'account_number' => $schedules[0]->account_number,
            'schedules' => $schedules->map(fn ($schedule) => [
                'net_pay' => number_format($schedule->net_pay, 2, '.', ','),
                'month' => "{$schedule->month_name} {$schedule->year}",
                'bank_name' => $schedule->bank_name,
                'account_number' => $schedule->account_number,
            ]),
        ];

        return new LoanResourceCollection($data);
    }
}
