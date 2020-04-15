<?php

namespace App\Http\Controllers;

use App\User;
use App\Payroll;
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
            return back()->with('error', "You Cannot Create Another Audit Payroll for $date->monthName $date->year");
        }

        $attributes = array_merge($attributes, ['user_id' => $user->id]);

        $payroll = $user->auditPayrolls()->create($attributes);

        $this->createAuditMdaSchedules($payroll, $user);

        return redirect()->route('audit_payroll.index')
                         ->with('success', "Payroll for $date->monthName $date->year Created Successfully");
    }


    public function createAuditMdaSchedules(AuditPayroll $payroll, User $user)
    {
        $beneficiary_types = $user->domain->beneficiaryTypes;

        $beneficiary_types->each(function($beneficiary_type) use ($payroll){
            $mdas = $beneficiary_type->mdas;
            $mdas->each(function($mda) use ($payroll){
                $payroll->auditMdaSchedules()->create([
                    'mda_id' => $mda->id,
                    'mda_name' => $mda->name,
                ]);
            });
        });
    }
}
