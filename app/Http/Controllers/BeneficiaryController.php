<?php

namespace App\Http\Controllers;

use App\Beneficiary;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class BeneficiaryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $filters = Request::all('search');
        $beneficiaries = Auth::user()->domain
            ->beneficiaries()
            ->with(['bank', 'mda_detail'])
            ->filters(Request::only('search'))
            ->paginate()
            ->transform(fn ($beneficiaries) => [
                'id' => $beneficiaries->id,
                'name' => $beneficiaries->name,
                'verification_number' => $beneficiaries->verification_number,
                'active' => $beneficiaries->active,
                'account_number' => $beneficiaries->bank->account_number,
                'bank_name' => $beneficiaries->bank->bankable->name,
                'mda' => $beneficiaries->mda_detail->mda->name,
                'sub_mda' => $beneficiaries->mda_detail->sub_mda->name,
                'sub_sub_mda' => $beneficiaries->mda_detail->sub_sub_mda->name,
                'designation' => $beneficiaries->work_detail->designation->name,
                'grade_level' => '',
                'step' => '',
            ]);

        return Inertia::render('Beneficiary/Index', [
            'filters' => $filters,
            'beneficiaries' => $beneficiaries,
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
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Beneficiary  $employee
     * @return \Illuminate\Http\Response
     */
    public function show(Beneficiary $employee)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Beneficiary  $employee
     * @return \Illuminate\Http\Response
     */
    public function edit(Beneficiary $employee)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Beneficiary  $employee
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Beneficiary $employee)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Beneficiary  $employee
     * @return \Illuminate\Http\Response
     */
    public function destroy(Beneficiary $employee)
    {
        //
    }
}
