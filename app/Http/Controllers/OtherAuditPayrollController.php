<?php

namespace App\Http\Controllers;

use App\Exceptions\WrongScheduleException;
use App\Imports\OtherScheduleImport;
use App\Models\OtherAuditPayrollCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use function back;
use function redirect;

class OtherAuditPayrollController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'payment_title' => ['required', 'string'],
            'paycomm_tenece' => ['required', 'boolean'],
            'paycomm_fidelity' => ['required', 'boolean'],
            'payment_type_id' => ['required', 'exists:payment_types,id'],
            'audit_payroll_id' => ['required', 'exists:audit_payrolls,id'],
        ]);

        OtherAuditPayrollCategory::create($data);

        $message = "Other Payroll Category for $request->payment_title Created Successfully";

        return redirect()
            ->route('audit_payroll.index')
            ->with('success', $message);
    }

    public function storeSchedule(Request $request)
    {
        $data = $request->validate([
            'other_audit_payroll_category_id' => ['required', 'numeric', 'exists:other_audit_payroll_categories,id'],
            'schedule_file' => ['required', 'mimes:xlsx,xls'],
        ]);

        $other_payroll_category = OtherAuditPayrollCategory::find($data['other_audit_payroll_category_id']);

        if ($other_payroll_category->uploaded) {
            return back()->with('error', "Schedule Already Uploaded for $other_payroll_category->payment_title");
        }

        $file_path = Storage::putFile('other_schedules', $data['schedule_file']);

        try {
            (new OtherScheduleImport($other_payroll_category, $file_path))->import($file_path);
        } catch (WrongScheduleException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\ErrorException $e) {
            return back()->with('error', 'Attached File is not a valid Other Pay Schedule '.$e->getMessage());
        } catch (\Exception $e) {
            return back()->with('error', 'Something Went Wrong! Please Contact Administrator '.$e->getMessage());
        }

        $confirm_upload = $other_payroll_category->auditOtherPaySchedules;

        if ($confirm_upload->isEmpty()) {
            $headers = 'SN | NAME | DESCRIPTION | AMOUNT | ACCOUNT NUMBER | BANK ';
            $message = "Upload Failed: Ensure Heading has {$headers}";

            return back()->with('error', $message);
        }

        $other_payroll_category->scheduleUploaded($file_path);

        return redirect()->back()->with('success', 'Other Payment Schedule Successfully Uploaded');
    }
}
