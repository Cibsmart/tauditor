<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\AuditPayroll;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AutoPayScheduleExport;
use App\Actions\GenerateAutoPayScheduleAction;
use function back;
use function array_push;

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
        $mdas = $audit_payroll->auditMdaSchedules;

        $file_names = [];

        foreach ($mdas as $mda) {
            $sub_mdas = $mda->auditSubMdaSchedules()->autopayGenerated()->get();

            foreach ($sub_mdas as $sub_mda) {

                $name = $sub_mda->sub_mda_name;
                $month = $sub_mda->month();
                $year = $sub_mda->year();

                $file_name = "$name $month $year.xlsx";

                array_push($file_names, $file_name);

                (new AutoPayScheduleExport)->forSubMda($sub_mda)->store($file_name);
            }
        }

        return back()->with('success', 'Download Started');
    }
}
