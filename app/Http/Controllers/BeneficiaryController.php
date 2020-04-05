<?php

namespace App\Http\Controllers;

use App\Beneficiary;
use Inertia\Inertia;
use Inertia\Response;
use App\StructuredSalary;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Database\Eloquent\Relations\MorphTo;

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
        $beneficiaries = Auth::user()
                             ->beneficiaries()
                             ->with($this->relationships())
                             ->filters(Request::only('search'))
                             ->paginate()
                             ->transform(fn (Beneficiary $beneficiary) => [
                                 'id' => $beneficiary->id,
                                 'name' => $beneficiary->name,
                                 'verification_number' => $beneficiary->verification_number,
                                 'active' => $beneficiary->active,
                                 'account_number' => $beneficiary->accountNumber(),
                                 'bank_name' => $beneficiary->bankName(),
                                 'mda' => $beneficiary->mdaName(),
                                 'sub_mda' => $beneficiary->subMdaName(),
                                 'sub_sub_mda' => $beneficiary->subSubMdaName(),
                                 'designation' => $beneficiary->designationName(),
                                 'grade_level' => $beneficiary->gradeLevelName(),
                                 'step' => $beneficiary->stepName(),
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

    public function relationships()
    {
        // Relationships Eager Loaded with Beneficiaries
        // to avoid multiple Database Round Trip
        return [
            'bankDetail',
            'mdaDetail.mda',
            'mdaDetail.subMda',
            'mdaDetail.subSubMda',
            'workDetail.designation',
            'salaryDetail.payable' => function(MorphTo $morphTo){
                $morphTo->morphWith([StructuredSalary::class => ['cadreStep.cadre.gradeLevel', 'cadreStep.step']]);
            }
        ];
    }
}
