<?php

namespace App\Http\Controllers;

use App\Beneficiary;
use Inertia\Inertia;
use Inertia\Response;
use App\StructuredSalary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use App\ViewModels\BeneficiaryViewModel;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use function array_merge;

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
                                 'id'                  => $beneficiary->id,
                                 'name'                => $beneficiary->name,
                                 'verification_number' => $beneficiary->verification_number,
                                 'active'              => $beneficiary->status->active,
                                 'account_number'      => $beneficiary->accountNumber(),
                                 'bank_name'           => $beneficiary->bankName(),
                                 'mda'                 => $beneficiary->mdaName(),
                                 'sub_mda'             => $beneficiary->subMdaName(),
                                 'sub_sub_mda'         => $beneficiary->subSubMdaName(),
                                 'designation'         => $beneficiary->designationName(),
                                 'grade_level'         => $beneficiary->gradeLevelName(),
                                 'step'                => $beneficiary->stepName(),
                             ]);

        return Inertia::render('Beneficiary/Index', [
            'filters'       => $filters,
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
        $user = auth()->user();
        $data = (new BeneficiaryViewModel($user))->data();

        return Inertia::render('Beneficiary/Create', [
            'beneficiary_view_model' => $data,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return RedirectResponse|\Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $domain_id = auth()->user()->domain->id;

        $attributes = $request->validate([
            'last_name'           => ['required', 'string'],
            'first_name'          => ['required', 'string'],
            'middle_name'         => ['nullable', 'string'],
            'date_of_birth'       => ['required'],
            'gender_id'           => ['required', 'string', 'max:1'],
            'marital_status_id'   => ['required', 'string', 'max:2'],
            'state_id'            => ['required', 'integer', 'min:1'],
            'local_government_id' => ['required', 'integer', 'min:1'],
            'phone_number'        => ['required', 'string'],
            'email'               => ['nullable', 'email'],
            'address_line_1'      => ['required', 'string'],
            'address_line_2'      => ['nullable', 'string'],
            'address_city'        => ['required', 'string'],
            'address_state'       => ['required', 'string'],
            'address_country'     => ['required', 'string'],
            'beneficiary_type_id' => ['required', 'string', 'min:1'],
            'pensioner'           => ['required', 'boolean'],
        ]);

        $attributes = array_merge($attributes, ['domain_id' => $domain_id]);

        $beneficiary = Beneficiary::create($attributes);

        $beneficiary->status()->create();

        return back()->with('success', "Beneficiary Created Successfully");
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
     * @param  Request  $request
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
            'salaryDetail.payable' => function (MorphTo $morphTo) {
                $morphTo->morphWith([StructuredSalary::class => ['cadreStep.cadre.gradeLevel', 'cadreStep.step']]);
            },
        ];
    }
}
