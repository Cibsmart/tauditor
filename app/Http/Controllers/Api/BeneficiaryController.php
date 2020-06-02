<?php

namespace App\Http\Controllers\Api;

use App\Domain;
use Carbon\Carbon;
use App\AuditPayroll;
use App\AuditPaySchedule;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\PayloadCollection;
use App\Http\Resources\BeneficiaryResource;
use Symfony\Component\HttpFoundation\Response;
use function response;

class BeneficiaryController extends Controller
{

    public function index($domain, $year, $month)
    {
        $domain = Domain::find($domain);

        if (! $domain) {
            return response()->json([
                'status' => false,
                'message' => 'Domain Does not Exist'
            ])->setStatusCode(Response::HTTP_NOT_FOUND);
        }

        if (Str::length($year) !== 4) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid Year'
            ])->setStatusCode(Response::HTTP_NOT_FOUND);
        }

        if (Str::length($month) !== 2) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid Month'
            ])->setStatusCode(Response::HTTP_NOT_FOUND);
        }

        $payroll = AuditPayroll::query()
                                ->where('domain_id', '=', $domain->id)
                                ->where('year', '=', $year)
                                ->where('month', '=', $month)
                                ->first();

        if (! $payroll) {
            return response()->json([
                'status' => false,
                'message' => 'Cannot Complete Request'
            ])->setStatusCode(Response::HTTP_NOT_FOUND);
        }

        $schedules = AuditPaySchedule::schedules($payroll)->limit(2)->get();

        if ($schedules->count() < 1) {
            return response()->json([
                'status' => true,
                'message' => 'No Record Found'
            ])->setStatusCode(Response::HTTP_NOT_FOUND);
        }

        $data = [
            'address' => $domain->name,
            'nigerians' => $schedules->count(),
            'schedules' => $schedules
        ];

        return new PayloadCollection($data);
    }

    public function invalid()
    {
        return response()->json([
            'status' => false,
            'message' => 'Invalid Request'
        ])->setStatusCode(Response::HTTP_NOT_FOUND);
    }
}
