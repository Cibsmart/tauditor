<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PayeResourceCollection;
use App\Http\Resources\PayloadCollection;
use App\Models\AuditPayroll;
use App\Models\AuditPaySchedule;
use App\Models\Domain;
use function array_diff;
use function collect;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use function in_array;
use function response;
use Symfony\Component\HttpFoundation\Response;

class BeneficiaryController extends Controller
{
    public function index($domain, $year, $month, $type)
    {
        $domain = Domain::find($domain);

        $staff_types = [
            'state' => ['staff', 'pension'],
            'jaac'  => ['lgea', 'lgsc', 'pension'],
        ];

        if (! $domain) {
            return response()->json([
                'status'  => false,
                'message' => 'Domain Does not Exist',
            ])->setStatusCode(Response::HTTP_NOT_FOUND);
        }

        if (Str::length($year) !== 4) {
            return response()->json([
                'status'  => false,
                'message' => 'Invalid Year',
            ])->setStatusCode(Response::HTTP_NOT_FOUND);
        }

        if (Str::length($month) !== 2) {
            return response()->json([
                'status'  => false,
                'message' => 'Invalid Month',
            ])->setStatusCode(Response::HTTP_NOT_FOUND);
        }

        if (! in_array($type, $staff_types[$domain->id])) {
            return response()->json([
                'status'  => false,
                'message' => 'Invalid Staff Type',
            ])->setStatusCode(Response::HTTP_NOT_FOUND);
        }

        $payroll = AuditPayroll::query()
                               ->where('domain_id', '=', $domain->id)
                               ->where('year', '=', $year)
                               ->where('month', '=', $month)
                               ->first();

        if (! $payroll) {
            return response()->json([
                'status'  => false,
                'message' => 'Cannot Complete Request',
            ])->setStatusCode(Response::HTTP_NOT_FOUND);
        }

        $schedules = AuditPaySchedule::schedules($payroll, $type)->get();

        if ($schedules->count() < 1) {
            return response()->json([
                'status'  => true,
                'message' => 'No Record Found',
            ])->setStatusCode(Response::HTTP_NOT_FOUND);
        }

        $data = [
            'address'   => $domain->name,
            'nigerians' => $schedules->count(),
            'schedules' => $schedules,
        ];

        return new PayloadCollection($data);
    }

    public function payeYear(Request $request)
    {
        [$vno, $vno_ok] = $this->validateVno($request->vno);
        [$year, $year_ok] = $this->validateYear($request->year);

        if ($vno_ok || $year_ok) {
            return response()->json([
                'status'  => '03',
                'message' => 'Invalid Request Data',
            ])->setStatusCode(Response::HTTP_NOT_FOUND);
        }

        $schedules = AuditPaySchedule::allSchedules()
                                     ->where('verification_number', $vno)
                                     ->where('year', $year)
                                     ->orderBY('audit_payrolls.month')
                                     ->get();

        if ($schedules->isEmpty()) {
            return response()->json([
                'status'  => '02',
                'message' => 'No Record Found',
            ])->setStatusCode(Response::HTTP_NOT_FOUND);
        }

        $beneficiary = $schedules->first();

        [$surname, $firs_name, $middle_name] = $this->splitName($beneficiary->beneficiary_name);

        $data = [
            'surname'             => $surname,
            'first_name'          => $firs_name,
            'middle_name'         => $middle_name,
            'mda'                 => $beneficiary->mda_name,
            'verification_number' => $beneficiary->verification_number,
            'account_number'      => $beneficiary->account_number,
            'bank_code'           => $beneficiary->bank_code,
            'schedules'           => $schedules,
        ];

        return new PayeResourceCollection($data);
    }

    public function payeMonth(Request $request)
    {
        [$vno, $vno_ok] = $this->validateVno($request->vno);
        [$year, $year_ok] = $this->validateYear($request->year);
        [$month, $month_ok] = $this->validateMonth($request->month);

        if ($vno_ok || $year_ok || $month_ok) {
            return response()->json([
                'status'  => '03',
                'message' => 'Invalid Request Data',
            ])->setStatusCode(Response::HTTP_NOT_FOUND);
        }

        $schedules = AuditPaySchedule::allSchedules()
                                     ->where('verification_number', $vno)
                                     ->where('year', $year)
                                     ->where('audit_payrolls.month', $month)
                                     ->limit(1)
                                     ->get();

        if ($schedules->isEmpty()) {
            return response()->json([
                'status'  => '02',
                'message' => 'No Record Found',
            ])->setStatusCode(Response::HTTP_NOT_FOUND);
        }

        $beneficiary = $schedules->first();

        [$surname, $firs_name, $middle_name] = $this->splitName($beneficiary->beneficiary_name);

        $data = [
            'surname'             => $surname,
            'first_name'          => $firs_name,
            'middle_name'         => $middle_name,
            'mda'                 => $beneficiary->mda_name,
            'verification_number' => $beneficiary->verification_number,
            'account_number'      => $beneficiary->account_number,
            'bank_code'           => $beneficiary->bank_code,
            'schedules'           => $schedules,
        ];

        return new PayeResourceCollection($data);
    }

    public function invalid()
    {
        return response()->json([
            'status'  => '07',
            'message' => 'Bad Request',
        ])->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    protected function splitName($name)
    {
        $names = Str::of($name)->trim()
                    ->explode(' ');

        $surname = $names->last();
        $first_name = $names->first();
        $middle_name = array_diff($names->all(), [$first_name, $surname]);

        $middle_name = Str::upper(Str::of($this->formatContent($middle_name))
                          ->replace(',', ' '));

        return [$surname, $first_name, $middle_name];
    }

    protected function formatContent($data)
    {
        return collect($data)->join(',');
    }

    protected function validateVno($vno)
    {
        $vno = Str::upper(Str::of($vno)->trim()->replace(' ', ''));

        $beneficiary = AuditPaySchedule::query()->where('verification_number', $vno)->get();

        return [$vno, $beneficiary->isEmpty()];
    }

    protected function validateMonth($month)
    {
        $months = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];

        $month = Str::upper(Str::of($month)->trim()->replace(' ', ''));

        return [$month,  ! in_array($month, $months)];
    }

    protected function validateYear($year)
    {
        $year = Str::upper(Str::of($year)->trim()->replace(' ', ''));

        return [$year,  Str::length($year) !== 4];
    }
}
