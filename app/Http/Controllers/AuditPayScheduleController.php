<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\AuditPaySchedule;
use Illuminate\Http\Request;
use App\AuditSubMdaSchedule;
use App\Imports\PayScheduleImport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Imports\PensionPayScheduleImport;
use App\Exceptions\WrongScheduleException;

class AuditPayScheduleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(AuditSubMdaSchedule $audit_sub_mda_schedule)
    {
        $audit_mda_schedule = $audit_sub_mda_schedule->auditMdaSchedule;
        $schedules = $audit_sub_mda_schedule->auditPaySchedules()
                                            ->paginate()
                                            ->transform(fn (AuditPaySchedule $schedule) => [
                                                'id'                  => $schedule->id,
                                                'verification_number' => $schedule->verification_number,
                                                'beneficiary_name'    => $schedule->beneficiary_name,
                                                'bank_name'           => $schedule->bank_name,
                                                'account_number'      => $schedule->account_number,
                                                'cadre'               => $schedule->beneficiary_cadre,
                                                'designation'         => $schedule->designation,
                                                'net_pay'             => number_format($schedule->net_pay, 2),
                                                // 12,000.00
                                                'paid'                => $schedule->paid,
                                                'month'               => $schedule->month,
                                                'year'                => $schedule->year,
                                                'mda_name'            => $schedule->mda_name,
                                                'department_name'     => $schedule->department_name,
                                            ]);

        return Inertia::render('AuditPaySchedules/Index', [
            'schedules'          => $schedules,
            'audit_mda_schedule' => $audit_mda_schedule->id,
            'audit_payroll_category'      => $audit_mda_schedule->auditPayrollCategory->id,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'audit_sub_mda' => ['required', 'numeric', 'exists:audit_sub_mda_schedules,id'],
            'schedule_file' => ['required', 'mimes:xlsx,xls'],
        ]);

        $audit_sub_mda = AuditSubMdaSchedule::find($request->audit_sub_mda);

        if ($audit_sub_mda->uploaded) {
            return back() - with('error', 'Pay Schedule Already Uploaded for Sub Mda');
        }

        $file_path = Storage::putFile('schedules', $request->schedule_file);

        $pension = $audit_sub_mda->auditMdaSchedule->pension;

        try {
            if ($pension) {
                (new PensionPayScheduleImport($audit_sub_mda, $file_path))->import($file_path);
            } else {
                (new PayScheduleImport($audit_sub_mda, $file_path))->import($file_path);
            }
        } catch (WrongScheduleException $e) {
            return back()->with('error', 'Check the Pay Schedule File, If it is for this Month and for the MDA');
        } catch (\ErrorException $e) {
            return back()->with('error', 'Attached File is not a valid Pay Schedule');
        } catch (\Exception $e) {
            return back()->with('error', 'Something Went Wrong! Please Contact Administrator');
        }

        $this->auditPayScheduleUploaded($audit_sub_mda, $file_path);

        return redirect()->back()->with('success', 'Payment Schedule Successfully Uploaded');
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
        $schedule->auditMdaSchedule->auditPayrollCategory->auditMdaScheduleWasUpdated();
    }
}
