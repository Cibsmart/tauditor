<?php

namespace App\Http\Controllers;

use App\Exceptions\WrongScheduleException;
use App\Imports\LeaveScheduleImport;
use App\Imports\PayScheduleImport;
use App\Imports\PensionPayScheduleImport;
use App\Models\AuditPaySchedule;
use App\Models\AuditSubMdaSchedule;
use function back;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

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
        $data = $request->validate([
            'audit_sub_mda' => ['required', 'numeric', 'exists:audit_sub_mda_schedules,id'],
            'schedule_file' => ['required', 'mimes:xlsx,xls'],
        ]);

        $audit_sub_mda = AuditSubMdaSchedule::find($request->audit_sub_mda);

        if ($audit_sub_mda->uploaded) {
            return back()->with('error', 'Pay Schedule Already Uploaded for Sub Mda');
        }

        $file_path = Storage::putFile('schedules', $request->schedule_file);

        $payment_type = $audit_sub_mda->auditMdaSchedule->auditPayrollCategory->payment_type_id;

        try {
            switch ($payment_type) {
                case 'pen':
                    (new PensionPayScheduleImport($audit_sub_mda, $file_path))->import($file_path);
                    break;
                case 'lev':
                    (new LeaveScheduleImport($audit_sub_mda, $file_path))->import($file_path);
                    break;
                case 'sal':
                    (new PayScheduleImport($audit_sub_mda, $file_path))->import($file_path);
                    break;
            }
        } catch (WrongScheduleException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\ErrorException $e) {
            return back()->with('error', 'Attached File is not a valid Pay Schedule ' . $e->getMessage());
        } catch (\Exception $e) {
            return back()->with('error', 'Something Went Wrong! Please Contact Administrator ' . $e->getMessage());
        }

        $confirm_upload = $audit_sub_mda->auditPaySchedules;

        if ($confirm_upload->isEmpty()) {
            $headers = 'ID | NAME | GRADE | DESIGNATION | B/S | BANK | ACCT | CODE | ALLOWANCES | TOTAL ALLW | GROSS PAY | DUES | TOTAL DUES | DEDUCTION | TOTAL DED | NET PAY';
            $message = "Upload Failed: Ensure Heading has {$headers}";

            return back()->with('error', $message);
        }

        $this->auditPayScheduleUploaded($audit_sub_mda, $file_path);

        return redirect()->back()->with('success', 'Payment Schedule Successfully Uploaded');
    }

    public function auditPayScheduleUploaded(AuditSubMdaSchedule $schedule, $file_path)
    {
//        $schedule->uploaded = 1;
        $schedule->user_id = Auth::id();
        $schedule->file_path = $file_path;
        $schedule->total_net_pay = $schedule->totalNetPay();
        $schedule->head_count = $schedule->headCount();

        $schedule->save();

        $schedule->auditMdaSchedule->auditSubMdaScheduleWasUpdated();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  AuditSubMdaSchedule  $audit_sub_mda_schedule
     * @return \Illuminate\Http\RedirectResponse|void
     */
    public function destroy(AuditSubMdaSchedule $audit_sub_mda_schedule)
    {
        if ($this->isArchived($audit_sub_mda_schedule)) {
            return redirect()->back()->with('error', 'You Cannot Re-Upload Archived Pay Schedules');
        }

        //Delete Pay Schedules, Autopay and Analysis Report
        $audit_sub_mda_schedule->auditPaySchedules()->delete();
        $audit_sub_mda_schedule->autopaySchedules()->delete();
        $audit_sub_mda_schedule->microfinanceSchedules()->delete();
        $audit_sub_mda_schedule->auditReports()->delete();

        //Notify Sub MDA Schedule that there is an update
        $audit_sub_mda_schedule->payScheduleWasCleared();

        return redirect()->back()->with('success', 'Pay Schedule Successfully Cleared and Ready for Re-Upload');
    }

    protected function isArchived(AuditSubMdaSchedule $audit_sub_mda_schedule)
    {
        $now = now();
        $payroll = $audit_sub_mda_schedule->auditMdaSchedule->auditPayrollCategory->auditPayroll;

        return $payroll->month !== $now->month || $payroll->year !== $now->year;
    }
}
