<?php

namespace App\Http\Controllers;

use App\Mda;
use App\Payroll;
use App\PaySchedule;
use Inertia\Inertia;
use Illuminate\Http\Request;
use function back;
use function number_format;

class PayScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  Payroll  $payroll
     * @param  Mda  $mda
     * @return \Illuminate\Http\RedirectResponse|\Inertia\Response
     */
    public function index(Payroll $payroll, Mda $mda)
    {
        if (! $payroll->generated) {
            return back()->with('error', 'Yet to Run Payroll for $date->monthName $date->year"');
        }

        $schedules = $payroll->schedules()
                             ->paginate()
                             ->transform(fn (PaySchedule $schedule) => [
                                 'id'               => $schedule->id,
                                 'beneficiary_name' => $schedule->beneficiary_name,
                                 'beneficiary_code' => $schedule->beneficiary_code,
                                 'mda'              => $schedule->mda_name,
                                 'sub_mda'          => $schedule->sub_mda_name,
                                 'sub_sub_mda'      => $schedule->sub_sub_mda_name,
                                 'account_number'   => $schedule->account_number,
                                 'bank_name'        => $schedule->bank_name,
                                 'net_pay'          => number_format($schedule->net_pay, 2),
                             ]);

        return Inertia::render('PaySchedules/Index', [
            'schedules' => $schedules,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return void
     */
    public function store(Request $request)
    {
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\PaySchedule  $paySchedule
     * @return \Illuminate\Http\Response
     */
    public function show(PaySchedule $paySchedule)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\PaySchedule  $paySchedule
     * @return \Illuminate\Http\Response
     */
    public function edit(PaySchedule $paySchedule)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\PaySchedule  $paySchedule
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PaySchedule $paySchedule)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\PaySchedule  $paySchedule
     * @return \Illuminate\Http\Response
     */
    public function destroy(PaySchedule $paySchedule)
    {
        //
    }
}
