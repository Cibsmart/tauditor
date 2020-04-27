<?php

namespace App\Http\Controllers;

use App\Payroll;
use Carbon\Carbon;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use function sleep;
use function compact;
use function redirect;

class PayrollController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        $this->authorize('index', Payroll::class);

        $payrolls = Auth::user()->payrolls()->latest()
                        ->paginate()
                        ->transform(fn (Payroll $payroll) => [
                            'id' => $payroll->id,
                            'month' => $payroll->month_name,
                            'year' => $payroll->year,
                            'approved' => $payroll->approved,
                            'archived' => $payroll->archived,
                            'generated' => $payroll->dateGenerated(),
                            'generated_by' => $payroll->generatedBy(),
                        ]);

        return Inertia::render('Payroll/Index', [
            'payrolls' => $payrolls,
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('create', Payroll::class);

        $date = Carbon::now();

        $user = Auth::user();

        $attributes = [
            'month' => $date->month,
            'month_name' => $date->monthName,
            'year' => $date->year,
        ];

        $payroll = $user->payrolls()->where($attributes)->first();

        if ($payroll) {
            return back()->with('error', "You Cannot Regenerate Payroll for $date->monthName $date->year");
        }

        $user->payrolls()->create($attributes);

        return redirect()->route('payroll.index')
                         ->with('success', "Payroll for $date->monthName $date->year Created Successfully");
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Payroll  $payroll
     * @return \Illuminate\Http\Response
     */
    public function show(Payroll $payroll)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Payroll  $payroll
     * @return \Illuminate\Http\Response
     */
    public function edit(Payroll $payroll)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Payroll  $payroll
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Payroll $payroll)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Payroll  $payroll
     * @return \Illuminate\Http\Response
     */
    public function destroy(Payroll $payroll)
    {
        //
    }
}
