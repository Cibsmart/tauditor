<?php

namespace App\Http\Controllers;

use App\Models\OtherAuditPayrollCategory;
use App\Actions\GenerateAutopayOtherScheduleAction;

class OtherAuditAutopayController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function generate(OtherAuditPayrollCategory $other_audit_payroll_category)
    {
        $category = $other_audit_payroll_category;
        $title = $category->payment_title;

        if ($category->autopay_status !== 'pending') {
            $message = "Autopay Schedule Cannot be Generated for $title ";
            return back()->with('error', $message);
        }

        $category->setAutopayStatus('running');

        //TODO: Restore when done
//        GenerateAutopayForOtherSchedule::dispatch($category);

        (new GenerateAutopayOtherScheduleAction())->execute($category);

        $message = "Autopay Schedule Generation for $title is Running, Refresh for Update";

        return back()->with('success', $message);
    }
}
