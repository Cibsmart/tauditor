<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Imports\PayScheduleImport;

class AuditPayScheduleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request)
    {
        $request->validate([
            'audit_sub_mda' => ['required', 'numeric', 'exists:audit_sub_mda_schedules,id'],
            'schedule_file' => ['required', 'mimes:xlsx,xls'],
        ]);

        $file_path = Storage::putFile('schedules', $request->schedule_file);

        try {
            $this->processPaySchedule($file_path, $request->audit_sub_mda);
        } catch (ErrorException $e) {
            return back()->with('error', 'Attached File is not a valid Pay Schedule');
        } catch (Exception $e) {
            return back()->with('error', 'Something Went Wrong! Please Contact Administrator');
        }

        return redirect()->back()->with('success', 'Payment Schedule Successfully Uploaded');
    }

    public function processPaySchedule($file_path, $audit_sub_mda)
    {
        (new PayScheduleImport($file_path, $audit_sub_mda))->import($file_path);
    }
}
