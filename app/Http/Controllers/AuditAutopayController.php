<?php

namespace App\Http\Controllers;

use ZipArchive;
use Inertia\Inertia;
use App\AuditPayroll;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\File;
use App\Exports\AutoPayScheduleExport;
use Illuminate\Support\Facades\Storage;
use App\Actions\GenerateAutoPayScheduleAction;
use function back;
use function response;
use function array_push;
use function public_path;
use function storage_path;

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
                $count++;
            }
        }

        $message = "$message $count MDAs, View MDAs for Details";

        return back()->with('success', $message);
    }

    public function download(AuditPayroll $audit_payroll)
    {
        $directory = $this->createFiles($audit_payroll);

        $zipped_file = $this->prepareDownload($directory);

        return response()->download(public_path($zipped_file))->deleteFileAfterSend();

//        return back()->with('success', 'Download Started');
    }

    public function createFiles(AuditPayroll $audit_payroll)
    {
        $mdas = $audit_payroll->auditMdaSchedules;

        $domain = $audit_payroll->domain->code;

        $month = $audit_payroll->month_name;

        $year = $audit_payroll->year;

        $directory = "$domain AUTOPAY SCHEDULE - $month $year";

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
        $zip_file = "$directory.zip";

        $zip = new ZipArchive();

        $zip->open($zip_file, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        $files = Storage::disk('local')->files($directory);

        $path = storage_path($directory);

        foreach ($files as $file)
        {
            $file_name = Str::of($file)->basename();

            $path = Storage::disk('local')->path($file);

            $zip->addFile($path, $file_name);
        }

        $zip->close();

        return $zip_file;
    }


}
