<?php

namespace App\Http\Controllers;

use App\Beneficiary;
use Inertia\Inertia;
use Inertia\Response;
use App\StructuredSalary;
use App\LocalGovernment;
use App\State;
use App\Domain;
use App\Gender;
use App\MaritalStatus;
use App\BeneficiaryType;
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
        $filters = request()->all('search');
        $beneficiaries = Auth::user()
                             ->beneficiaries()
                             ->with($this->relationships())
                             ->filters(request()->only('search'))
                             ->paginate()
                             ->transform(fn (Beneficiary $beneficiary) => [
                                 'id' => $beneficiary->id,
                                 'name' => $beneficiary->name,
                                 'verification_number' => $beneficiary->verification_number,
                                 'active' => $beneficiary->status->active,
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
     * @return Response
     */
    public function create()
    {
        $lga = LocalGovernment::all();
        $states = State::all();
        $domains = Domain::all();
        $gender = Gender::all();
        $marital_status = MaritalStatus::all();
        $beneficiary_types = BeneficiaryType::all();

        $filters = Request::all('search');
        return Inertia::render('Beneficiary/Create',[
            'filters' => $filters,
            'lga' => $lga,
            'states' => $states,
            'domains' => $domains,
            'gender' => $gender,
            'marital_status' => $marital_status,
            'beneficiary_types' => $beneficiary_types,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function store()
    {
      //dd(Request::all('first_name'));
        try{

            Beneficiary::create(
                Request::validate([
                    'verification_number'=>'nullable|string',
                    'last_name' => 'required|string',
                    'first_name' => 'required|string',
                    'middle_name' => 'nullable|string',
                    'date_of_birth' => 'required|date',
                    'gender_id' => 'nullable|integer|min:1',
                    'marital_status_id' => 'nullable|integer|min:1',
                    'state_id' => 'nullable|integer|min:1',
                    'local_government_id' => 'nullable|integer|min:1',
                    'phone_number' => 'nullable|string',
                    'email' => 'nullable|email',
                    'address_line_1' => 'nullable|string',
                    'address_line_2' => 'nullable|string',
                    'address_city' => 'nullable|string',
                    'address_state' => 'nullable|string',
                    'address_country' => 'nullable|string',
                    'domain_id' => 'required|integer|min:1',
                    'beneficiary_type_id' => 'required|integer|min:1',
                    'active' => 'nullable',
                ])
            );

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
