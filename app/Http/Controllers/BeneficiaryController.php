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
        $beneficiaries = Auth::user()->domain
            ->beneficiaries()
            ->with($this->relationships())
            ->filters(Request::only('search'))
            ->paginate()
            ->transform(fn (Beneficiary $beneficiaries) => [
                'id' => $beneficiaries->id,
                'name' => $beneficiaries->name,
                'verification_number' => $beneficiaries->verification_number,
                'active' => $beneficiaries->active,
                'account_number' => $beneficiaries->accountNumber(),
                'bank_name' => $beneficiaries->bankName(),
                'mda' => $beneficiaries->mdaName(),
                'sub_mda' => $beneficiaries->subMdaName(),
                'sub_sub_mda' => $beneficiaries->subSubMdaName(),
                'designation' => $beneficiaries->designationName(),
                'grade_level' => $beneficiaries->gradeLevelName(),
                'step' => $beneficiaries->stepName(),
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
        $filters = Request::all('search'); 
        return Inertia::render('Beneficiary/Create', ['filters' => $filters ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'last_name' => 'required',
            'first_name' => 'required',
            'date_of_birth' => 'required',
            'domain_id' => 'required',
            'beneficiary_type_id' => 'required',
        ]);

        try{
            
            Beneficiary::Create(['last_name'=>$request->last_name,'first_name'=>$request->first_name,
            'date_of_birth'=>$request->date_of_birth,'domain_id'=>$request->domain_id,
            'beneficiary_type_id'=>$request->beneficiary_type_id
            ]);

            return back()->with('success',"Beneficiary Created");
        }catch(Exception $e){
            return back()->with('errors',$e);
        }
        
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
