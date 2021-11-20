<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\PaymentType;
use Illuminate\Http\Request;
use App\Models\AuditPayroll;
use Illuminate\Support\Facades\Auth;
use App\Imports\OtherScheduleImport;
use Illuminate\Support\Facades\Storage;
use App\Models\OtherAuditPayrollCategory;
use App\Exceptions\WrongScheduleException;
use function back;
use function redirect;

class OtherAuditPayrollController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $payrolls = Auth::user()->auditPayrolls()->orderBy('year', 'desc')->orderBy('month', 'desc')
                        ->paginate()
                        ->transform(fn(AuditPayroll $payroll) => [
                            'id'           => $payroll->id,
                            'month'        => $payroll->month_name,
                            'year'         => $payroll->year,
                            'created_by'   => $payroll->createdBy(),
                            'date_created' => $payroll->dateCreated(),
                            'is_current'   => $payroll->currentMonth(),
                            'categories'   => $payroll->otherPaymentCategories->transform(fn($category) => [
                                'id'              => $category->id,
                                'payment_type_id' => $category->payment_type_id,
                                'payment_type'    => $category->paymentTypeName(),
                                'payment_title'   => $category->payment_title,
                                'total_amount'    => number_format($category->total_net_pay, 2),
                                'head_count'      => number_format($category->head_count),
                                'uploaded'        => $category->uploaded,
                                'tenece'          => $category->paycomm_tenece,
                                'fidelity'        => $category->paycomm_fidelity,
                                'color'           => $category->color,
                            ]),
                        ]);

        $payment_types = PaymentType::query()
                                    ->select('id', 'name')
                                    ->where('category', 'others')
                                    ->get();

        return Inertia::render('OtherAuditPayroll/Index', [
            'payrolls'      => $payrolls,
            'payment_types' => $payment_types,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'payment_title'    => ['required', 'string'],
            'paycomm_tenece'   => ['required', 'boolean'],
            'paycomm_fidelity' => ['required', 'boolean'],
            'payment_type_id'  => ['required', 'exists:payment_types,id'],
            'audit_payroll_id' => ['required', 'exists:audit_payrolls,id'],
        ]);


        OtherAuditPayrollCategory::create($data);

        $message = "Other Payroll Category for $request->payment_title Created Successfully";

        return redirect()
            ->route('other_audit_payroll.index')
            ->with('success', $message);
    }

    public function storeSchedule(Request $request)
    {
        $data = $request->validate([
            'other_audit_payroll_category_id' => ['required', 'numeric', 'exists:other_audit_payroll_categories,id'],
            'schedule_file'                   => ['required', 'mimes:xlsx,xls'],
        ]);

        $other_payroll_category = OtherAuditPayrollCategory::find($data['other_audit_payroll_category_id']);

        if ($other_payroll_category->uploaded) {
            return back()->with('error', "Schedule Already Uploaded for $other_payroll_category->payment_title");
        }

        $file_path = Storage::putFile('other_schedules', $data['schedule_file']);

        try{
            (new OtherScheduleImport($other_payroll_category, $file_path))->import($file_path);
        } catch (WrongScheduleException $e){
            return back()->with('error', $e->getMessage());
        } catch (\ErrorException $e){
            return back()->with('error', 'Attached File is not a valid Other Pay Schedule '.$e->getMessage());
        } catch (\Exception $e){
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
