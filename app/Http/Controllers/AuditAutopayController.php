<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\AuditPayroll;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Classes\ZipDirectory;
use Illuminate\Support\Facades\DB;
use App\Exports\MfbScheduleExport;
use Illuminate\Support\Facades\Auth;
use App\Exports\AutoPayScheduleExport;
use Illuminate\Support\Facades\Storage;
use App\Actions\GenerateAutoPayScheduleAction;

class AuditAutopayController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $payrolls = Auth::user()->auditPayrolls()->latest()
                        ->paginate()
                        ->transform(fn(AuditPayroll $payroll) => [
                            'id' => $payroll->id,
                            'month' => $payroll->month_name,
                            'year' => $payroll->year,
                            'created_by' => $payroll->createdBy(),
                            'date_created' => $payroll->dateCreated(),
                            'autopay_generated' => $payroll->autopay_generated,
                        ]);

        return Inertia::render('AuditAutoPay/Index', [
            'payrolls' => $payrolls,
        ]);
    }

    public function generate(AuditPayroll $audit_payroll)
    {
        $mdas = $audit_payroll->auditMdaSchedules;
        $message = 'Autopay Schedules Generated for ';
        $count = 0;

        foreach ($mdas as $mda) {
            $sub_mdas = $mda->auditSubMdaSchedules()->uploaded()->autopayNotGenerated()->get();

            foreach ($sub_mdas as $sub_mda) {
                DB::transaction(function () use ($sub_mda){
                    (new GenerateAutoPayScheduleAction())->execute($sub_mda);
                });

                $sub_mda->autopayGenerated();
                $count++;
            }
        }

        $message = "$message $count MDAs, View MDAs for Details";

        return back()->with('success', $message);
    }

    public function download(AuditPayroll $audit_payroll)
    {
        $year = $audit_payroll->year;
        $month = $audit_payroll->month_name;

        if($audit_payroll->noAutopaySchedule()){
            return back()->with('error', "Autopay Schedule for $month $year yet to be Generated, Click on Generate Schedules Below");
        }

        $directory = $this->createFiles($audit_payroll);

        $zipped_file = $this->prepareDownload($directory);

        return response()->download(public_path($zipped_file))->deleteFileAfterSend();
    }

    public function createFiles(AuditPayroll $audit_payroll)
    {
        $mdas = $audit_payroll->auditMdaSchedules;

        $domain = $audit_payroll->domain->code;

        $month = $audit_payroll->month_name;

        $year = $audit_payroll->year;

        $directory = "autopay/$domain AUTOPAY SCHEDULE - $month $year";

        foreach ($mdas as $mda) {
            $sub_mdas = $mda->auditSubMdaSchedules()->autopayGenerated()->get();

            foreach ($sub_mdas as $sub_mda) {

                $name = $sub_mda->sub_mda_name;

                $file_name = "$name $month $year.xlsx";

                $path = "$directory/$file_name";

                $autopay_file_exists = Storage::disk('local')->exists($path);

                if($autopay_file_exists){
                    continue;
                }

                (new AutoPayScheduleExport)->forSubMda($sub_mda)->store($path);
            }
        }

        return $directory;
    }

    public function prepareDownload($directory)
    {
        $path = Storage::disk('local')->path($directory);

        $zipped_file = Str::of($path)->basename()->append('.zip');

        ZipDirectory::zipDir($path, $zipped_file);

        return $zipped_file;
    }



    public function downloadMfb(AuditPayroll $audit_payroll)
    {
        $year = $audit_payroll->year;
        $month = $audit_payroll->month_name;

        if($audit_payroll->noAutopaySchedule()){
            return back()->with('error', "Autopay Schedule for $month $year yet to be Generated, Click on Generate Schedules Below");
        }

        if($audit_payroll->noMfbSchedule()){
            return back()->with('error', "No Beneficiary Used Microfinance in $month $year Payment Schedule");
        }

        $directory = $this->createMfbFiles($audit_payroll);

        $zipped_file = $this->prepareDownload($directory);

        return response()->download(public_path($zipped_file))->deleteFileAfterSend();
    }

    public function createMfbFiles(AuditPayroll $audit_payroll)
    {
        $mdas = $audit_payroll->auditMdaSchedules;

        $domain = $audit_payroll->domain->code;

        $month = $audit_payroll->month_name;

        $year = $audit_payroll->year;

        $directory = "microfinance/$domain MFB SCHEDULE - $month $year";

        foreach ($mdas as $mda) {
            $sub_mdas = $mda->auditSubMdaSchedules()
                            ->autopayGenerated()
                            ->hasMicrofinance()
                            ->get();

            foreach ($sub_mdas as $sub_mda) {

                $mfbs = $sub_mda->microfinanceSchedules()->with('microfinanceBank')
                                ->select(DB::raw(' audit_sub_mda_schedule_id, micro_finance_bank_id'))
                                ->groupBy('audit_sub_mda_schedule_id', 'micro_finance_bank_id')
                                ->get();

                $sub_mda_name = $sub_mda->sub_mda_name;

                foreach ($mfbs as $mfb) {
                    $mfb = $mfb->microfinanceBank;

                    $mfb_name = $mfb->name;

                    $file_name = "$sub_mda_name $month $year.xlsx";

                    $path = "$directory/$mfb_name/$file_name";

                    $mfb_file_exists = Storage::disk('local')->exists($path);

                    if($mfb_file_exists){
                        continue;
                    }

                    (new MfbScheduleExport)->forMfbs($mfb)->inSubMda($sub_mda)->store($path);
                }
            }
        }

        return $directory;
    }
}
