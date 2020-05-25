<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\AuditPayroll;
use App\AuditMdaSchedule;
use Illuminate\Support\Str;
use App\AuditSubMdaSchedule;
use App\Classes\ZipDirectory;
use App\AuditPayrollCategory;
use Illuminate\Support\Facades\DB;
use App\Exports\MfbScheduleExport;
use Illuminate\Support\Facades\Auth;
use App\Exports\AutoPayScheduleExport;
use Illuminate\Support\Facades\Storage;
use App\Actions\GenerateAutoPayScheduleAction;
use function back;
use function auth;
use function route;
use function redirect;
use function number_format;

class AuditAutopayController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $payrolls = Auth::user()->auditPayrolls()->orderBy('year', 'desc')->orderBy('month', 'desc')
                        ->paginate()
                        ->transform(fn (AuditPayroll $payroll) => [
                            'id'                => $payroll->id,
                            'month'             => $payroll->month_name,
                            'year'              => $payroll->year,
                            'created_by'        => $payroll->createdBy(),
                            'date_created'      => $payroll->dateCreated(),
                            'autopay_generated' => $payroll->autopay_generated,
                            'categories'        => $payroll->auditPaymentCategories->transform(fn ($category) => [
                                'id'              => $category->id,
                                'payment_type_id' => $category->payment_type_id,
                                'payment_type'    => $category->paymentTypeName(),
                                'payment_title'   => $category->payment_title,
                            ]),
                        ]);

        return Inertia::render('AuditAutoPay/Index', [
            'payrolls' => $payrolls,
        ]);
    }

    public function show(AuditPayrollCategory $audit_payroll_category)
    {
        if ($audit_payroll_category->domain()->id !== auth()->user()->domain->id) {
            return redirect(route('audit_autopay.index'));
        }

        $schedules = $audit_payroll_category->auditMdaSchedules()
                                            ->with(['mda', 'auditPayrollCategory.auditPayroll.domain'])
                                            ->orderBy('mda_id')
                                            ->paginate()
                                            ->transform(fn (AuditMdaSchedule $schedule) => [
                                                'id'           => $schedule->id,
                                                'sub_mda_id'   => $schedule->auditSubMdaSchedules()->first()->id,
                                                'payroll_id'   => $audit_payroll_category->id,
                                                'mda_id'       => $schedule->mda_id,
                                                'mda_name'     => $schedule->mda->name,
                                                'total_amount' => number_format($schedule->autopayTotalAmount(), 2),
                                                'head_count'   => number_format($schedule->autopayItemCount()),
                                                'month'        => $audit_payroll_category->auditPayroll->month_name,
                                                'year'         => $audit_payroll_category->auditPayroll->year,
                                                'uploaded'     => $schedule->autopay_uploaded,
                                                'generated'    => $schedule->autopay_generated,
                                                'pension'      => $schedule->pension,
                                                'has_sub'      => $schedule->has_sub,
                                                'domain'       => $schedule->auditPayrollCategory->auditPayroll->domain->name,
                                            ]);

        return Inertia::render('AuditAutoPay/Show', [
            'schedules' => $schedules,
        ]);
    }

    public function detail(AuditMdaSchedule $audit_mda_schedule)
    {
        if ($audit_mda_schedule->domain()->id !== auth()->user()->domain->id) {
            return redirect(route('audit_autopay.index'));
        }

        $schedules = $audit_mda_schedule->auditSubMdaSchedules()
                                        ->with('auditMdaSchedule.auditPayrollCategory.auditPayroll')
                                        ->paginate()
                                        ->transform(fn (AuditSubMdaSchedule $schedule) => [
                                            'id'           => $schedule->id,
                                            'sub_mda_name' => $schedule->sub_mda_name,
                                            'total_amount' => number_format($schedule->autopayTotalAmount(), 2),
                                            'item_count'   => number_format($schedule->autopayItemCount()),
                                            'month'        => $audit_mda_schedule->auditPayrollCategory->auditPayroll->month_name,
                                            'year'         => $audit_mda_schedule->auditPayrollCategory->auditPayroll->year,
                                            'uploaded'     => $schedule->autopay_uploaded,
                                            'generated'     => $schedule->autopay_generated,
                                            'mda_name'     => $audit_mda_schedule->mda_name,
                                        ]);

        return Inertia::render('AuditAutoPay/Detail', [
            'schedules'              => $schedules,
            'audit_payroll_category' => $audit_mda_schedule->auditPayrollCategory->id,
        ]);
    }

    public function generate(AuditPayrollCategory $audit_payroll_category)
    {
        $mdas = $audit_payroll_category->auditMdaSchedules;
        $title = $audit_payroll_category->payment_title;
        $message = "Autopay Schedules Generated for $title ";
        $count = 0;

        foreach ($mdas as $mda) {
            $sub_mdas = $mda->auditSubMdaSchedules()->uploaded()->autopayNotGenerated()->get();

            foreach ($sub_mdas as $sub_mda) {
                DB::transaction(function () use ($sub_mda) {
                    (new GenerateAutoPayScheduleAction())->execute($sub_mda);
                });

                $sub_mda->autopayGenerated();
                $count++;
            }
        }

        if ($count === 0) {
            $message = "No New Schedule Has Been Uploaded for $title";
            return back()->with('error', $message);
        }

        $mda_string = Str::plural('MDA', $count);

        $message = "$message, $count $mda_string Affected, View MDAs for Details";

        return back()->with('success', $message);
    }

    public function download(AuditPayrollCategory $audit_payroll_category)
    {
        $month_year = $audit_payroll_category->monthYear();

        if ($audit_payroll_category->noAutopaySchedule()) {
            return back()->with(
                'error',
                "Autopay Schedule for $month_year yet to be Generated, Click on Generate to Generated Autopay Schedule"
            );
        }

        $directory = $this->createFiles($audit_payroll_category);

        $zipped_file = $this->prepareDownload($directory);

        $headers = ['Content-Type' => 'application/zip'];

        return response()->download(public_path($zipped_file), null, $headers)
                         ->deleteFileAfterSend();
    }

    public function createFiles(AuditPayrollCategory $audit_payroll_category)
    {
        $category = $audit_payroll_category->payment_title;

        $mdas = $audit_payroll_category->auditMdaSchedules;

        $month_year = $audit_payroll_category->monthYear();

        $directory = "autopay/$category - AUTOPAY SCHEDULE - $month_year";

        foreach ($mdas as $mda) {
            $sub_mdas = $mda->auditSubMdaSchedules()->autopayGenerated()->get();

            foreach ($sub_mdas as $sub_mda) {
                $name = $sub_mda->sub_mda_name;

                $file_name = "$name $month_year.xlsx";

                $path = "$directory/$file_name";

                $autopay_file_exists = Storage::disk('local')->exists($path);

                if ($autopay_file_exists) {
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

    public function downloadMfb(AuditPayrollCategory $audit_payroll_category)
    {
        $month_year = $audit_payroll_category->monthYear();

        if ($audit_payroll_category->noAutopaySchedule()) {
            return back()->with(
                'error',
                "Autopay Schedule for $month_year yet to be Generated, Click on Generate Schedules Below"
            );
        }

        if ($audit_payroll_category->noMfbSchedule()) {
            return back()->with('error', "No Beneficiary Used Microfinance in $month_year Payment Schedule");
        }

        $directory = $this->createMfbFiles($audit_payroll_category);

        $zipped_file = $this->prepareDownload($directory);

        $headers = ['Content-Type' => 'application/zip'];

        return response()->download(public_path($zipped_file), null, $headers)
                         ->deleteFileAfterSend();
    }

    public function createMfbFiles(AuditPayrollCategory $audit_payroll_category)
    {
        $category = $audit_payroll_category->payment_title;

        $mdas = $audit_payroll_category->auditMdaSchedules;

        $month_year = $audit_payroll_category->monthYear();

        $directory = "autopay/$category - MFB SCHEDULE - $month_year";

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

                    $file_name = "$sub_mda_name $month_year.xlsx";

                    $path = "$directory/$mfb_name/$file_name";

                    $mfb_file_exists = Storage::disk('local')->exists($path);

                    if ($mfb_file_exists) {
                        continue;
                    }

                    (new MfbScheduleExport)->forMfbs($mfb)->inSubMda($sub_mda)->store($path);
                }
            }
        }

        return $directory;
    }
}
