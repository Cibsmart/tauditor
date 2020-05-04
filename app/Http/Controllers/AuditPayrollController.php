<?php

namespace App\Http\Controllers;

use App\Mda;
use Carbon\Carbon;
use Inertia\Inertia;
use App\AuditPayroll;
use App\BeneficiaryType;
use App\AuditMdaSchedule;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\AuditPayrollCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AuditPayrollController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $payrolls = Auth::user()->auditPayrolls()->latest()
                        ->paginate()
                        ->transform(fn (AuditPayroll $payroll) => [
                            'id'           => $payroll->id,
                            'month'        => $payroll->month_name,
                            'year'         => $payroll->year,
                            'created_by'   => $payroll->createdBy(),
                            'date_created' => $payroll->dateCreated(),
                            'categories'   => $payroll->auditPaymentCategories->transform(fn ($category) => [
                                'id'              => $category->id,
                                'payment_type_id' => $category->payment_type_id,
                                'payment_type'    => $category->paymentTypeName(),
                                'payment_title'   => $category->payment_title,
                                'total_amount'   => $category->total_net_pay,
                                'head_count'   => $category->head_count,
                            ]),
                        ]);

        return Inertia::render('AuditPayroll/Index', [
            'payrolls' => $payrolls,
        ]);
    }

    public function store(Request $request)
    {
        $date = Carbon::now();

        $user = Auth::user();

        $attributes = [
            'month'      => $date->month,
            'month_name' => $date->monthName,
            'year'       => $date->year,
        ];

        $payroll = $user->auditPayrolls()->where($attributes)->first();

        if ($payroll) {
            return back()->with('error', "You Cannot Create Another Audit Payroll for $date->monthName $date->year");
        }

        $attributes = array_merge($attributes, ['user_id' => $user->id]);

        DB::transaction(function () use ($user, $attributes) {
            $payroll = $user->auditPayrolls()->create($attributes);

            $this->createPaymentCategories($payroll);
        });

        return redirect()->route('audit_payroll.index')
                         ->with('success', "Payroll for $date->monthName $date->year Created Successfully");
    }

    private function createPaymentCategories(AuditPayroll $payroll)
    {
        $domains = ['state' => 'ANSG', 'jaac' => 'ANLG'];
        $salary_beneficiary_types = $payroll->domain->beneficiaryTypes()
                                                    ->thatReceives($salary = 'sal')
                                                    ->get();

        $domain = $domains[$payroll->domain->id];
        $month = $payroll->month();

        $title = Str::upper("$domain $month salary");

        $category = $payroll->auditPaymentCategories()->create([
            'payment_type_id' => $salary,
            'payment_title'   => $title,
        ]);

        foreach ($salary_beneficiary_types as $salary_beneficiary_type) {
            $this->createAuditMdaSchedules($salary_beneficiary_type, $category);
        }

        $pension_beneficiary_types = $payroll->domain->beneficiaryTypes()
                                                     ->thatReceives($pension = 'pen')
                                                     ->get();

        $title = Str::upper("$domain $month pension");

        $category = $payroll->auditPaymentCategories()->create([
            'payment_type_id' => $pension,
            'payment_title'   => $title,
        ]);

        foreach ($pension_beneficiary_types as $salary_beneficiary_type) {
            $this->createAuditMdaSchedules($salary_beneficiary_type, $category);
        }
    }

    private function createAuditMdaSchedules(BeneficiaryType $beneficiary_type, AuditPayrollCategory $category)
    {
        $mdas = $beneficiary_type->mdas()->isActive()->get();
        $pensioners = $beneficiary_type->pensioners;

        foreach ($mdas as $mda) {
            $attributes = [
                'mda_id'   => $mda->id,
                'mda_name' => $mda->name,
                'pension'  => $pensioners,
            ];

            $audit_mda_schedule = $category->auditMdaSchedules()->create($attributes);

            $this->createAuditSubMdaSchedules($mda, $audit_mda_schedule);
        }
    }

    private function createAuditSubMdaSchedules(Mda $mda, AuditMdaSchedule $audit_mda_schedule)
    {
        //Only State Education Commission should have add subs
        if ($mda->code !== 'SEC') {
            $audit_mda_schedule->auditSubMdaSchedules()->create(['sub_mda_name' => $audit_mda_schedule->mda_name,]);
            return;
        }

        $sub_mdas = $mda->subs;

        $audit_mda_schedule->has_sub = 1;
        $audit_mda_schedule->save();

        foreach ($sub_mdas as $sub_mda) {
            $audit_mda_schedule->auditSubMdaSchedules()->create(['sub_mda_name' => $sub_mda->name,]);
        }

        return;
    }
}
