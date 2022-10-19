<?php

namespace App\Http\Controllers;

use App\Classes\ZipDirectory;
use App\Exports\AutoPayGroupScheduleExport;
use App\Exports\AutoPayScheduleExport;
use App\Exports\MfbGroupScheduleExport;
use App\Exports\MfbScheduleExport;
use App\Jobs\GenerateAutopaySchedules;
use App\Jobs\GenerateGroupSchedule;
use App\Models\AuditMdaSchedule;
use App\Models\AuditPayroll;
use App\Models\AuditPayrollCategory;
use App\Models\AuditSubMdaSchedule;
use App\Models\BeneficiaryType;
use App\Models\MdaScheduleView;
use App\Models\MicrofinanceBankSchedule;
use App\Models\OtherAuditPayrollCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class AuditAutopayController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $payrolls = Auth::user()->auditPayrolls()
                        ->orderBy('year', 'desc')
                        ->orderBy('month', 'desc')
                        ->paginate()
                        ->transform(fn (AuditPayroll $payroll) => [
                            'id'                => $payroll->id,
                            'month'             => $payroll->month_name,
                            'year'              => $payroll->year,
                            'created_by'        => $payroll->createdBy(),
                            'date_created'      => $payroll->dateCreated(),
                            'autopay_generated' => $payroll->autopay_generated,
                            'categories'        => $payroll->auditPaymentCategories
                                ->transform(function (AuditPayrollCategory $category) {
                                    $uploaded_count = $category->countOfMdasSchedulesUploaded();
                                    $autopay_count = $category->countOfMdasAutopayGenerated();
                                    $status = $category->autopay_status;
                                    $available = $uploaded_count - $autopay_count > 0;

                                    return [
                                        'id'              => $category->id,
                                        'payment_type_id' => $category->payment_type_id,
                                        'payment_type'    => $category->paymentTypeName(),
                                        'payment_title'   => $category->payment_title,
                                        'autopay_status'  => $category->autopay_status,
                                        'mda_count'       => $category->mdaCount(),
                                        'uploaded_count'  => $uploaded_count,
                                        'autopay_count'   => $autopay_count,
                                        'can_generate'    => $available && $status !== 'running',
                                        'viewable'        => $autopay_count > 0,
                                        'refreshable'     => $available && $status === 'running',
                                    ];
                                }),

                            'other_categories' => $payroll->otherPaymentCategories
                                ->transform(function (OtherAuditPayrollCategory $category) {
                                    $status = $category->autopay_status;
                                    $uploaded = $category->uploaded;
                                    $generated = $category->autopay_generated;

                                    return [
                                        'id'                => $category->id,
                                        'payment_type_id'   => $category->payment_type_id,
                                        'payment_type'      => $category->paymentTypeName(),
                                        'payment_title'     => $category->payment_title,
                                        'line_items'        => $category->autopaySchedules->count(),
                                        'autopay_status'    => $status,
                                        'autopay_generated' => $generated,
                                        'uploaded'          => $uploaded,
                                        'can_generate'      => $uploaded && ! $generated && $status !== 'running',
                                        'viewable'          => $uploaded && $generated,
                                        'refreshable'       => $uploaded && $status === 'running',
                                        'tenece'          => $category->paycomm_tenece,
                                        'fidelity'        => $category->paycomm_fidelity,
                                        'color'           => $category->color,
                                    ];
                                }),
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

        $schedules = $audit_payroll_category->auditMdaSchedules()->orderBy('mda_name')
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

        $schedules = $audit_mda_schedule->auditSubMdaSchedules()->orderBy('sub_mda_name')
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
                                            'generated'    => $schedule->autopay_generated,
                                            'mda_name'     => $audit_mda_schedule->mda_name,
                                        ]);

        return Inertia::render('AuditAutoPay/Detail', [
            'schedules'              => $schedules,
            'audit_payroll_category' => $audit_mda_schedule->auditPayrollCategory->id,
        ]);
    }

    public function generate(AuditPayrollCategory $category)
    {
        $domain = $category->domain();
        $title = $category->payment_title;
        $count = 0;

        if ($category->autopay_status !== 'pending') {
            $message = "No [New] Schedule Has Been Uploaded for $title";

            return back()->with('error', $message);
        }

//        $category->setAutopayStatus('running');

        if ($domain->group) {
            $beneficiary_types = MdaScheduleView::query()
                ->select('beneficiary_type')
                ->where('id', $category->id)
                ->where('uploaded', 1)
                ->whereNull('generated')
                ->groupBy('beneficiary_type')
                ->pluck('beneficiary_type');
            dd($beneficiary_types);

            foreach ($beneficiary_types as $beneficiary_type) {
                $type = BeneficiaryType::find($beneficiary_type);
                GenerateGroupSchedule::dispatchSync($domain, $category, $type);
            }
        } else {
            $mdas = $category->auditMdaSchedules;

            foreach ($mdas as $mda) {
                $sub_mdas = $mda->auditSubMdaSchedules()->uploaded()->autopayNotGenerated()->get();

                foreach ($sub_mdas as $sub_mda) {
                    GenerateAutopaySchedules::dispatch($domain, $sub_mda);
                    $count++;
                }
            }
        }
        dd('here');

        $message = "Autopay Schedule Generation for $title is Running, Refresh for Update";

        return back()->with('success', $message);
    }

    public function download(AuditPayrollCategory $category)
    {
        $month_year = $category->monthYear();
        $domain = $category->domain();

        if ($category->noAutopaySchedule()) {
            return back()->with(
                'error',
                "Autopay Schedule for $month_year yet to be Generated, Click on Generate to Generated Autopay Schedule"
            );
        }

        $directory = $domain->group
            ? $this->createGroupFiles($category)
            : $this->createFiles($category);

        $zipped_file = $this->prepareDownload($directory);

        $headers = ['Content-Type' => 'application/zip'];

        return response()->download(public_path($zipped_file), null, $headers)
                         ->deleteFileAfterSend();
    }

    public function createGroupFiles(AuditPayrollCategory $category)
    {
        $title = $category->payment_title;

        $month_year = $category->monthYear();

        $directory = "autopay/$title - AUTOPAY SCHEDULE - $month_year";

        $beneficiaryTypes = MdaScheduleView::query()
            ->select('beneficiary_type')
            ->where('id', $category->id)
            ->whereNotNull('generated')
            ->groupBy('beneficiary_type')
            ->pluck('beneficiary_type');

        foreach ($beneficiaryTypes as $beneficiaryType) {
            $type = BeneficiaryType::find($beneficiaryType);

            $name = $type->name;

            $file_name = "$name $month_year.xlsx";

            $path = "$directory/$file_name";

            $autopay_file_exists = Storage::disk('local')->exists($path);

//                if ($autopay_file_exists) {
//                    continue;
//                }

            (new AutoPayGroupScheduleExport())->forBeneficiaryType($category, $type)->store($path);
        }

        return $directory;
    }

    public function createFiles(AuditPayrollCategory $category)
    {
        $category = $category->payment_title;

        $mdas = $category->auditMdaSchedules;

        $month_year = $category->monthYear();

        $directory = "autopay/$category - AUTOPAY SCHEDULE - $month_year";

        foreach ($mdas as $mda) {
            $sub_mdas = $mda->auditSubMdaSchedules()->autopayGenerated()->get();

            foreach ($sub_mdas as $sub_mda) {
                $name = $sub_mda->sub_mda_name;

                $file_name = "$name $month_year AUTOPAY SCHEDULE.xlsx";

                $path = "$directory/$file_name";

                $autopay_file_exists = Storage::disk('local')->exists($path);

//                if ($autopay_file_exists) {
//                    continue;
//                }

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

    public function downloadMfb(AuditPayrollCategory $category)
    {
        $domain = $category->domain();
        $month_year = $category->monthYear();

        if ($category->noAutopaySchedule()) {
            return back()->with(
                'error',
                "Autopay Schedule for $month_year yet to be Generated, Click on Generate Schedules Below"
            );
        }

        if ($category->noMfbSchedule()) {
            return back()->with('error', "No Beneficiary Used Microfinance in $month_year Payment Schedule");
        }

        $directory = $domain->group
            ? $this->createGroupMfbFiles($category)
            : $this->createMfbFiles($category);

        $zipped_file = $this->prepareDownload($directory);

        $headers = ['Content-Type' => 'application/zip'];

        return response()->download(public_path($zipped_file), null, $headers)
                         ->deleteFileAfterSend();
    }

    public function createGroupMfbFiles(AuditPayrollCategory $category)
    {
        $title = $category->payment_title;

        $month_year = $category->monthYear();

        $directory = "autopay/$title - MFB SCHEDULE - $month_year";

        $query = MdaScheduleView::query()
            ->join('microfinance_bank_schedules', 'mda_schedule_views.sub_mda_id', '=', 'microfinance_bank_schedules.audit_sub_mda_schedule_id')
            ->where('mda_schedule_views.id', $category->id)
            ->whereNotNull('generated');

        $beneficiaryTypes = $query->select('beneficiary_type')->groupBy('beneficiary_type')->pluck('beneficiary_type');

        foreach ($beneficiaryTypes as $beneficiaryType) {

            $type = BeneficiaryType::find($beneficiaryType);

            $subMdas = $query->select('sub_mda_id')
                ->where('beneficiary_type', $beneficiaryType)
                ->groupBy('sub_mda_id')->pluck('sub_mda_id');

            $mfbs = MicrofinanceBankSchedule::with('microfinanceBank')
                ->select('micro_finance_bank_id')
                ->where('beneficiary_type_id', $beneficiaryType)
                ->whereIn('audit_sub_mda_schedule_id', $subMdas)
                ->groupBy('micro_finance_bank_id')
                ->get();

            foreach ($mfbs as $mfb) {
                $mfb = $mfb->microfinanceBank;

                $mfb_name = $mfb->name;

                $file_name = "$type->name $month_year.xlsx";

                $path = "$directory/$mfb_name/$file_name";

                $mfb_file_exists = Storage::disk('local')->exists($path);

//                    if ($mfb_file_exists) {
//                        continue;
//                    }

                (new MfbGroupScheduleExport())->forMfbs($mfb)->inBeneficiaryType($category, $type)->store($path);
            }
        }

        return $directory;
    }

    public function createMfbFiles(AuditPayrollCategory $category)
    {
        $category = $category->payment_title;

        $mdas = $category->auditMdaSchedules;

        $month_year = $category->monthYear();

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

//                    if ($mfb_file_exists) {
//                        continue;
//                    }

                    (new MfbScheduleExport)->forMfbs($mfb)->inSubMda($sub_mda)->store($path);
                }
            }
        }

        return $directory;
    }
}
