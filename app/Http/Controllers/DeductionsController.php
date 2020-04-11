<?php

namespace App\Http\Controllers;

use App\Deduction;
use App\DeductionType;
use App\DeductionName;
use App\FixedValue;
use App\PercentageValue;
use App\ComputedValue;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class DeductionsController extends Controller
{

    public function __construct(){
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        $filters = request()->all('search');
        $deductions =  Auth::user()
                            ->deductions()
                            ->filters(request()->only('search'))
                            ->paginate()
                            ->transform(fn(Deduction $deductions) => [
                                'id' => $deductions->id,
                                'name' => $deductions->name(),
                                'amount' => $deductions->amount(),
                                'value_type' => $deductions->valueType(),
                                'deduction_type' => $deductions->deductionType()->name,
                            ]);

        return Inertia::render('Deductions/Index', [
            'filters' => $filters,
            'deductions' => $deductions,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Inertia\Response
     */
    public function create()
    {
        $deductiontypes =  Auth::user()
        ->deductionstype()
        ->get();

        $deductionnames =  Auth::user()
            ->deductionsname()
            ->get();

        return Inertia::render('Deductions/create',
            ['deductiontypes' => $deductiontypes,
            'deductionnames' => $deductionnames,
            ]);
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
            'deductiontype' => ['required'],
            'deductionname' => ['required'],
            'value_type' => ['required'],
            'fixed_value' => ['nullable', 'integer', 'min:100', 'max:1000000'],
            'percentage_value' => ['nullable', 'integer', 'min:1', 'max: 50'],
        ]);

        $data = $request->all();

        if($data['value_type'] == "fixed_value" ){
            $valuable_f = FixedValue::create([
                'amount'=>$data['fixed_value'],
                ]);

                $valuable_id = $valuable_f->id;
        }
        elseif($data['value_type'] == "percentage_value" ){
            $valuable_p = PercentageValue::create([
                'percentage'=>$data['percentage_value'],
                ]);

            $valuable_id = $valuable_p->id;
        }
        elseif($data['value_type'] == "computed_value" ){
            $valuable_p = ComputedValue::create([
                'computer'=>'compute_'.$data['deductionname'],
                ]);

            $valuable_id = $valuable_p->id;
        }

        if(!(is_int($data['deductionname']))){
            $NewDeductionName = DeductionName::create([
                'deduction_type_id'=>$data['deductiontype'],
                'code'=>$data['deductionname'],
                'name'=>$data['deductionname'],
                'domain_id'=>Auth::user()->domain_id,
            ]);

            $NewDeductionName_id = $NewDeductionName->id;

            Deduction::create([
                'deduction_name_id'=>$NewDeductionName_id,
                'valuable_type'=>$data['value_type'],
                'valuable_id'=>$valuable_id,
                'domain_id'=>Auth::user()->domain_id,
            ]);

        }else{
            Deduction::create([
                'deduction_name_id'=>$data['deductionname'],
                'valuable_type'=>$data['value_type'],
                'valuable_id'=>$valuable_id,
                'domain_id'=>Auth::user()->domain_id,
            ]);
        }


        return redirect()->back()->withSuccess('Submitted Successfuly');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function relationships()
    {
        // Relationships Eager Loaded with Beneficiaries
        // to avoid multiple Database Round Trip
        return [
            'deductionDetails.deduction',
            'deductionName.deductionType',
        ];
    }
}
