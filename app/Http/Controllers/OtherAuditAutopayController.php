<?php

namespace App\Http\Controllers;

use App\Classes\ZipDirectory;
use App\Exports\AutopayOtherScheduleExport;
use App\Exports\MfbOtherScheduleExport;
use App\Jobs\GenerateAutopayForOtherSchedule;
use App\Models\OtherAuditPayrollCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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

        GenerateAutopayForOtherSchedule::dispatch($category);

        //        (new GenerateAutopayOtherScheduleAction())->execute($category);

        $message = "Autopay Schedule Generation for $title is Running, Refresh for Update";

        return back()->with('success', $message);
    }

    public function download(OtherAuditPayrollCategory $other_audit_payroll_category)
    {
        $category = $other_audit_payroll_category;

        $title = $category->payment_title;

        if (! $category->autopay_generated) {
            return back()->with(
                'error',
                "Autopay Schedule for $title yet to be Generated, Click on Generate to Generated Autopay Schedule"
            );
        }

        $directory = $this->createFiles($category);

        $zipped_file = $this->prepareDownload($directory);

        $headers = ['Content-Type' => 'application/zip'];

        return response()->download(public_path($zipped_file), null, $headers)
            ->deleteFileAfterSend();
    }

    public function createFiles(OtherAuditPayrollCategory $category)
    {
        $title = $category->payment_title;

        $month_year = $category->auditPayroll->month();

        $directory = "autopay/$title - AUTOPAY SCHEDULE - $month_year";

        $file_name = "$title $month_year.xlsx";

        $path = "$directory/$file_name";

        $autopay_file_exists = Storage::disk('local')->exists($path);

        //                if ($autopay_file_exists) {
        //                    continue;
        //                }

        (new AutopayOtherScheduleExport)->forOtherSchedules($category)->store($path);

        return $directory;
    }

    public function prepareDownload($directory)
    {
        $path = Storage::disk('local')->path($directory);

        $zipped_file = Str::of($path)->basename()->append('.zip');

        ZipDirectory::zipDir($path, $zipped_file);

        return $zipped_file;
    }

    public function downloadMfb(OtherAuditPayrollCategory $other_audit_payroll_category)
    {
        $category = $other_audit_payroll_category;

        $title = $category->payment_title;

        if (! $category->autopay_generated) {
            return back()->with(
                'error',
                "Autopay Schedule for $title yet to be Generated, Click on Generate Schedules Below"
            );
        }

        if ($category->noMfbSchedule()) {
            return back()->with('error', "No Beneficiary Used Microfinance in $title Payment Schedule");
        }

        $directory = $this->createMfbFiles($category);

        $zipped_file = $this->prepareDownload($directory);

        $headers = ['Content-Type' => 'application/zip'];

        return response()->download(public_path($zipped_file), null, $headers)
            ->deleteFileAfterSend();
    }

    public function createMfbFiles(OtherAuditPayrollCategory $category)
    {
        $title = $category->payment_title;

        $month_year = $category->auditPayroll->month();

        $directory = "autopay/$title - MFB SCHEDULE - $month_year";

        $mfbs = $category->microfinanceSchedules()->with('microfinanceBank')
            ->select(DB::raw('other_audit_payroll_category_id, micro_finance_bank_id'))
            ->groupBy('other_audit_payroll_category_id', 'micro_finance_bank_id')
            ->get();

        foreach ($mfbs as $mfb) {
            $mfb = $mfb->microfinanceBank;

            $mfb_name = $mfb->name;

            $file_name = "$title $month_year.xlsx";

            $path = "$directory/$mfb_name/$file_name";

            //            $mfb_file_exists = Storage::disk('local')->exists($path);
            //
            //                    if ($mfb_file_exists) {
            //                        continue;
            //                    }

            (new MfbOtherScheduleExport)->forMfbs($mfb)->inCategory($category)->store($path);
        }

        return $directory;
    }
}
