<?php

namespace App\Http\Controllers\Api;

use App\AuditPaySchedule;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\BeneficiaryResource;

class BeneficiaryController extends Controller
{

    public function index()
    {
        $schedules = AuditPaySchedule::query()->limit(10)->inRandomOrder()->get();

//        dd($schedules);

        return BeneficiaryResource::collection($schedules);
    }
}
