<?php

namespace App\Http\Controllers;

use App\Mda;
use App\User;
use App\Payroll;
use App\AuditPayroll;
use Carbon\Carbon;
use Inertia\Inertia;
use App\AuditMdaSchedule;
use Illuminate\Http\Request;
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

        DB::transaction(function() use ($user, $attributes){
            $payroll = $user->auditPayrolls()->create($attributes);

            $this->createAuditMdaSchedules($payroll, $user);
        });

        return redirect()->route('audit_payroll.index')
                         ->with('success', "Payroll for $date->monthName $date->year Created Successfully");
    }


    public function createAuditMdaSchedules(AuditPayroll $payroll, User $user)
    {
        $beneficiary_types = $user->domain->beneficiaryTypes;

        foreach ($beneficiary_types as $beneficiary_type) {
            $mdas = $beneficiary_type->mdas;
            $pensioners = $beneficiary_type->pensioners;

            foreach ($mdas as $mda) {
                $attributes = [
                    'mda_id' => $mda->id,
                    'mda_name' => $mda->name,
                    'pension' => $pensioners,
                ];

                $audit_mda_schedule = $payroll->auditMdaSchedules()->create($attributes);

                $this->createAuditSubMdaSchedules($mda, $audit_mda_schedule);
            }
        }
    }

    public function createAuditSubMdaSchedules(Mda $mda, AuditMdaSchedule $audit_mda_schedule)
    {
        //Only State Education Commission should have add subs
        if($mda->code !== 'SEC'){
            $audit_mda_schedule->auditSubMdaSchedules()->create(['sub_mda_name' => $audit_mda_schedule->mda_name,]);
            return;
        }

        $sub_mdas = $mda->subs;

        foreach ($sub_mdas as $sub_mda) {
            $audit_mda_schedule->auditSubMdaSchedules()->create(['sub_mda_name' => $sub_mda->name,]);
        }

        return;
    }
}
