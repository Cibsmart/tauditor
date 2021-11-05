<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Inertia\Inertia;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\AuditPayroll;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use function now;
use function back;
use function redirect;
use function array_merge;
use function number_format;

class OtherAuditPayrollController extends Controller
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
                            'id'           => $payroll->id,
                            'month'        => $payroll->month_name,
                            'year'         => $payroll->year,
                            'created_by'   => $payroll->createdBy(),
                            'date_created' => $payroll->dateCreated(),
                            'is_current' => $payroll->currentMonth(),
                            'can_add_leave' => $payroll->canAddLeaveAllowance(),
                            'categories'   => $payroll->auditPaymentCategories->transform(fn ($category) => [
                                'id'              => $category->id,
                                'payment_type_id' => $category->payment_type_id,
                                'payment_type'    => $category->paymentTypeName(),
                                'payment_title'   => $category->payment_title,
                                'total_amount'    => number_format($category->total_net_pay, 2),
                                'head_count'      => number_format($category->head_count),
                            ]),
                        ]);

        return Inertia::render('AuditPayroll/Index', ['payrolls' => $payrolls,]);
    }

    public function store()
    {
        $date = now();

        $user = Auth::user();

        $month_name = Str::upper($date->monthName);
        $year = $date->year;

        $attributes = [
            'month'      => $date->month,
            'month_name' => $month_name,
            'year'       => $year,
            'timestamp'  => Carbon::parse("25 $month_name $year"),
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
}
