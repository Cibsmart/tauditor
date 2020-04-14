<?php

namespace App\Http\Controllers;

use App\AuditPayroll;
use Carbon\Carbon;
use Inertia\Inertia;
use Illuminate\Http\Request;
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
                                ->transform(fn(AuditPayroll $payroll) => [
                                    'id' => $payroll->id,
                                    'month' => $payroll->month_name,
                                    'year' => $payroll->year,
                                    'created_by' => $payroll->createdBy(),
                                    'date_created' => $payroll->dateCreated(),
                                ]);

        return Inertia::render('AuditPayroll/Index', [
            'payrolls' => $payrolls,
        ]);
    }

    public function store()
    {
        $date = Carbon::now();

        $user = Auth::user();

        $attributes = [
            'month' => $date->month,
            'month_name' => $date->monthName,
            'year' => $date->year,
        ];

        $payroll = $user->auditPayrolls()->where($attributes)->first();

        if($payroll)
        {
            return back()->with('error', "You Cannot Regenerate Payroll for $date->monthName $date->year");
        }

        $attributes = array_merge($attributes, ['user_id' => $user->id]);

        $user->auditPayrolls()->create($attributes);

        return redirect()->route('audit_payroll.index')
                         ->with('success', "Payroll for $date->monthName $date->year Created Successfully");
    }
}
