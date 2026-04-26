<?php

namespace App\Http\Controllers;

use App\Models\AuditPayroll;
use App\Models\PaymentType;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;

use function now;
use function number_format;
use function redirect;

class AuditPayrollController extends Controller
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
                'id' => $payroll->id,
                'month' => $payroll->month_name,
                'year' => $payroll->year,
                'created_by' => $payroll->createdBy(),
                'date_created' => $payroll->dateCreated(),
                'is_current' => $payroll->currentMonth(),
                'can_add_leave' => $payroll->canAddLeaveAllowance(),
                'categories' => $payroll->auditPaymentCategories->transform(fn ($category) => [
                    'id' => $category->id,
                    'payment_type_id' => $category->payment_type_id,
                    'payment_type' => $category->paymentTypeName(),
                    'payment_title' => $category->payment_title,
                    'total_amount' => number_format($category->total_net_pay, 2),
                    'head_count' => number_format($category->head_count),
                    'totalAmount' => $category->total_net_pay,
                    'headCount' => $category->head_count,
                ]),
                'other_categories' => $payroll->otherPaymentCategories->transform(fn ($category) => [
                    'id' => $category->id,
                    'payment_type_id' => $category->payment_type_id,
                    'payment_type' => $category->paymentTypeName(),
                    'payment_title' => $category->payment_title,
                    'total_amount' => number_format($category->total_net_pay, 2),
                    'head_count' => number_format($category->head_count),
                    'uploaded' => $category->uploaded,
                    'tenece' => $category->paycomm_tenece,
                    'fidelity' => $category->paycomm_fidelity,
                    'color' => $category->color,
                    'totalAmount' => $category->total_net_pay,
                    'headCount' => $category->head_count,
                ]),
            ]);

        $payment_types = PaymentType::query()
            ->select('id', 'name')
            ->where('category', 'others')
            ->get();

        return Inertia::render('AuditPayroll/Index', [
            'payrolls' => $payrolls,
            'payment_types' => $payment_types,
        ]);
    }

    public function store()
    {
        $date = now();

        $user = Auth::user();

        $month_name = Str::upper($date->monthName);
        $year = $date->year;

        $attributes = [
            'month' => $date->month,
            'month_name' => $month_name,
            'year' => $year,
            'timestamp' => Carbon::parse("25 $month_name $year"),
        ];

        $payroll = $user->auditPayrolls()->where($attributes)->first();

        $message = "You Cannot Create Another Audit Payroll for $date->monthName $date->year";

        if ($payroll) {
            return back()->with('error', $message);
        }

        $attributes = array_merge($attributes, ['user_id' => $user->id]);

        DB::transaction(function () use ($user, $attributes) {
            $payroll = $user->auditPayrolls()->create($attributes);

            $this->createPaymentCategories($payroll);
        });

        $message = "Payroll for $date->monthName $date->year Created Successfully";

        return redirect()->route('audit_payroll.index')
            ->with('success', $message);
    }

    public function leave(AuditPayroll $audit_payroll)
    {
        $this->createLeaveCategories($audit_payroll);

        $message = "Annual Leave Allowance Category Added for $audit_payroll->monthName $audit_payroll->year Successfully";

        return redirect()->route('audit_payroll.index')
            ->with('success', $message);
    }

    protected function createAuditMdaSchedules($beneficiary_type, $category)
    {
        $mdas = $beneficiary_type->mdas()->isActive()->get();

        $pensioners = $beneficiary_type->pensioners;

        foreach ($mdas as $mda) {
            $attributes = [
                'mda_id' => $mda->id,
                'mda_name' => $mda->name,
                'pension' => $pensioners,
            ];

            $audit_mda_schedule = $category->auditMdaSchedules()->create($attributes);

            $this->createAuditSubMdaSchedules($mda, $audit_mda_schedule);
        }
    }

    protected function createAuditSubMdaSchedules($mda, $audit_mda_schedule)
    {
        // Only State Education Commission should have add subs
        if ($mda->code !== 'PPSSC') {
            $audit_mda_schedule->auditSubMdaSchedules()
                ->create(['sub_mda_name' => $audit_mda_schedule->mda_name]);

            return;
        }

        $sub_mdas = $mda->subs;

        $audit_mda_schedule->has_sub = 1;
        $audit_mda_schedule->save();

        foreach ($sub_mdas as $sub_mda) {
            $audit_mda_schedule->auditSubMdaSchedules()
                ->create(['sub_mda_name' => $sub_mda->name]);
        }

    }

    private function createPaymentCategories(AuditPayroll $payroll)
    {
        $domains = ['state' => 'ANSG', 'jaac' => 'ANLG'];
        $salary_beneficiary_types = $payroll->domain->beneficiaryTypes()
            ->thatReceives($salary = 'sal')
            ->get();

        $domain = $domains[$payroll->domain->id];
        $month = $payroll->month();

        if ($payroll->domain->id === 'state') {
            $title = Str::upper("$domain $month salary");

            $category = $payroll->auditPaymentCategories()->create([
                'payment_type_id' => $salary,
                'payment_title' => $title,
                'staff_type' => 'staff',
            ]);

            foreach ($salary_beneficiary_types as $beneficiary_type) {
                $this->createAuditMdaSchedules($beneficiary_type, $category);
            }
        }

        if ($payroll->domain->id === 'jaac') {
            foreach ($salary_beneficiary_types as $beneficiary_type) {
                $beneficiary_type_id = $beneficiary_type->id;
                $title = Str::upper("$domain $beneficiary_type_id $month salary");

                $category = $payroll->auditPaymentCategories()->create([
                    'payment_type_id' => $salary,
                    'payment_title' => $title,
                    'staff_type' => $beneficiary_type->id,
                ]);

                $this->createAuditMdaSchedules($beneficiary_type, $category);
            }
        }

        $pension_beneficiary_types = $payroll->domain->beneficiaryTypes()
            ->thatReceives($pension = 'pen')
            ->get();

        $title = Str::upper("$domain $month pension");

        $category = $payroll->auditPaymentCategories()->create([
            'payment_type_id' => $pension,
            'payment_title' => $title,
            'staff_type' => 'pensioners',
        ]);

        foreach ($pension_beneficiary_types as $beneficiary_type) {
            $this->createAuditMdaSchedules($beneficiary_type, $category);
        }
    }

    private function createLeaveCategories(AuditPayroll $payroll)
    {
        $domains = ['state' => 'ANSG', 'jaac' => 'ANLG'];
        $salary_beneficiary_types = $payroll->domain->beneficiaryTypes()
            ->thatReceives($salary = 'lev')
            ->get();

        $domain = $domains[$payroll->domain->id];
        $year = $payroll->year;

        if ($payroll->domain->id === 'state') {
            $title = Str::upper("$domain $year Annual Leave Allowance");

            $category = $payroll->auditPaymentCategories()->create([
                'payment_type_id' => $salary,
                'payment_title' => $title,
                'staff_type' => 'staff',
            ]);

            foreach ($salary_beneficiary_types as $beneficiary_type) {
                $this->createAuditMdaSchedules($beneficiary_type, $category);
            }
        }

        if ($payroll->domain->id === 'jaac') {
            foreach ($salary_beneficiary_types as $beneficiary_type) {
                $beneficiary_type_id = $beneficiary_type->id;
                $title = Str::upper("$domain $beneficiary_type_id $year Annual Leave Allowance");

                $category = $payroll->auditPaymentCategories()->create([
                    'payment_type_id' => $salary,
                    'payment_title' => $title,
                    'staff_type' => $beneficiary_type->id,
                ]);

                $this->createAuditMdaSchedules($beneficiary_type, $category);
            }
        }
    }
}
