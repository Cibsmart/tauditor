<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;

class AuditPayScheduleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $schedules = [];

        return Inertia::render('AuditPaySchedule/Index', [
            'schedules' => $schedules,
        ]);
    }

}
