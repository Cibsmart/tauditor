<?php

namespace App\Http\Controllers;

use App\Deduction;
use App\FixedValue;
use Inertia\Inertia;
use App\ComputedValue;
use App\DeductionType;
use App\DeductionName;
use App\PercentageValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $deduction_types =  Auth::user()->deductionTypes()->get();
        $deduction_names =  Auth::user()->deductionNames()->get();

        return Inertia::render('Deductions/Create', [
            'deduction_types' => $deduction_types,
            'deduction_names' => $deduction_names,
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
            'deduction_type' => ['required'],
            'deduction_name' => ['required'],
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
                'computer'=>'compute_'.$data['deduction_name'],
                ]);

            $valuable_id = $valuable_p->id;
        }

        if(!(is_int($data['deduction_name']))){
            $NewDeductionName = DeductionName::create([
                'deduction_type_id'=>$data['deduction_type'],
                'code'=>$data['deduction_name'],
                'name'=>$data['deduction_name'],
                'domain_id'=>Auth::user()->domain_id,
            ]);

            $NewDeductionName_id = $NewDeductionName->id;

            Deduction::create([
                'deduction_name_id'=> $NewDeductionName_id,
                'valuable_type'=> $data['value_type'],
                'valuable_id'=> $valuable_id,
                'domain_id'=>Auth::user()->domain_id,
            ]);

        }else{
            Deduction::create([
                'deduction_name_id'=>$data['deduction_name'],
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
