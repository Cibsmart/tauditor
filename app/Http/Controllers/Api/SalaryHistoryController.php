<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use function now;
use function collect;
use function request;
use function response;

class SalaryHistoryController extends Controller
{
    public function index(Request $request)
    {
        $bvn = $request->bvn;

        // TODO: Uncomment
//        $this->logRequest($bvn);



        return ['bvn' => $bvn];
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
