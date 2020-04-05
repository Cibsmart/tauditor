<?php

namespace App\Http\Controllers;

use App\Payroll;
use App\PaySchedule;
use Illuminate\Http\Request;
use function redirect;

class PayScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
