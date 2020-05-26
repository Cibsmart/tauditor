<?php

namespace App\Http\Controllers\Api;

use App\AuditPaySchedule;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BeneficiaryController extends Controller
{

    public function index()
    {
        $schedules = AuditPaySchedule::query();

        return $schedules;
    }
}
