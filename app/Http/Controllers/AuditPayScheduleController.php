<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use App\AuditSubMdaSchedule;
use App\Imports\PayScheduleImport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Exceptions\WrongScheduleException;
use function back;

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

        $audit_sub_mda = AuditSubMdaSchedule::find($request->audit_sub_mda);

        if($audit_sub_mda->uploaded){
            return back()-with('error', 'Pay Schedule Already Uploaded for Sub Mda');
        }

        $file_path = Storage::putFile('schedules', $request->schedule_file);

        $this->processPaySchedule($audit_sub_mda, $file_path);

        $this->auditPayScheduleUploaded($audit_sub_mda, $file_path);

        return redirect()->back()->with('success', 'Payment Schedule Successfully Uploaded');
    }

    private function processPaySchedule(AuditSubMdaSchedule $audit_sub_mda, $file_path)
    {
        try {
            (new PayScheduleImport($audit_sub_mda, $file_path))->import($file_path);
        } catch (WrongScheduleException $e) {
            return back()->with('error', 'Check the Pay Schedule File, If it is for this Month and for the MDA');
        } catch (\ErrorException $e) {
            return back()->with('error', 'Attached File is not a valid Pay Schedule');
        } catch (\Exception $e) {
            return back()->with('error', 'Something Went Wrong! Please Contact Administrator');
        }
    }

    public function auditPayScheduleUploaded(AuditSubMdaSchedule $schedule, $file_path)
    {
        $schedule->uploaded = 1;
        $schedule->user_id = Auth::id();
        $schedule->file_path = $file_path;
        $schedule->total_net_pay = $schedule->totalNetPay();
        $schedule->head_count = $schedule->headCount();

        $schedule->save();

        $schedule->auditMdaSchedule->auditSubMdaScheduleWasUpdated();
    }
}
