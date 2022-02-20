<?php

namespace App\Http\Controllers;

use App\Classes\ZipDirectory;
use App\Exports\MfbScheduleExport;
use App\Models\AuditPayroll;
use App\Models\AuditPayrollCategory;
use App\Models\MicroFinanceBank;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class MfbScheduleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();

        $mfb = $user->microfinanceBank->micro_finance_bank_id;

        $payrolls = AuditPayroll::query()
                                ->select('audit_payrolls.*')
                                ->payrolls()
                                ->orderByMonth()
                                ->where('micro_finance_bank_id', $mfb)
                                ->distinct()
                                ->paginate()
                                ->transform(fn (AuditPayroll $payroll) => [
                                    'id'         => $payroll->id,
                                    'month'      => $payroll->month_name,
                                    'year'       => $payroll->year,
                                    'categories' => $payroll->auditPaymentCategories()
                                                            ->categories()
                                                            ->select('audit_payroll_categories.*')
                                                            ->where('micro_finance_bank_id', $mfb)
                                                            ->distinct()
                                                            ->orderBy('payment_type_id', 'desc')->get()
                                                            ->transform(fn (AuditPayrollCategory $category) => [
                                                                'id'              => $category->id,
                                                                'payment_type_id' => $category->payment_type_id,
                                                                'payment_type'    => $category->paymentTypeName(),
                                                                'payment_title'   => $category->payment_title,
                                                                'autopay_status'  => $category->autopay_status,
                                                                'mda_count'       => $category->mfbMdaCount($mfb),
                                                                'mfb_id'          => $mfb,
                                                            ]),
                                ]);

        return Inertia::render('MFBSchedule/Index', [
            'payrolls' => $payrolls,
        ]);
    }

    public function download(AuditPayrollCategory $category, MicroFinanceBank $mfb)
    {
        $this->authorize('view', $mfb);

        $directory = $this->createMfbFiles($category, $mfb);

        $zipped_file = $this->prepareDownload($directory);

        $headers = ['Content-Type' => 'application/zip'];

        return response()->download(public_path($zipped_file), null, $headers)
                         ->deleteFileAfterSend();
    }

    public function show()
    {
        $this->authorize('view', $mfb);

        $user = Auth::user();
    }

    public function createMfbFiles(AuditPayrollCategory $category, MicroFinanceBank $mfb)
    {
        $title = $category->payment_title;

        $mdas = $category->auditMdaSchedules()
                         ->mfbSchedules()
                         ->select('audit_mda_schedules.*')
                         ->where('micro_finance_bank_id', $mfb->id)
                         ->distinct()
                         ->get();

        $month_year = $category->monthYear();

        $directory = "microfinance_schedules/{$title} - MFB SCHEDULE - {$month_year}/{$mfb->name}";

        foreach ($mdas as $mda) {
            $sub_mdas = $mda->auditSubMdaSchedules()
                            ->mfbSchedules()
                            ->select('audit_sub_mda_schedules.*')
                            ->where('micro_finance_bank_id', $mfb->id)
                            ->distinct()
                            ->get();

            foreach ($sub_mdas as $sub_mda) {
                $sub_mda_name = $sub_mda->sub_mda_name;

                $file_name = "$sub_mda_name $month_year.xlsx";

                $path = "{$directory}/{$file_name}";

                $mfb_file_exists = Storage::disk('local')->exists($path);

                if ($mfb_file_exists) {
                    continue;
                }

                (new MfbScheduleExport)->forMfbs($mfb)->inSubMda($sub_mda)->store($path);
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
}
