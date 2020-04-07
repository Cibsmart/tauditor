<?php

namespace App\Http\Controllers;

use Redirect;
use App\Deduction;
use App\DeductionType;
use App\DeductionName;
use App\FixedValue;
use App\PercentageValue;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Request;
use function compact;

class DeductionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        $filters = Request::all('search');
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
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $deductiontypes =  Auth::user()
            ->deductionstype()
            ->paginate()
            ->transform(fn(DeductionType $deductiontypes) => [
                'deduction_type_id' => $deductiontypes->id,
                'deduction_type' => $deductiontypes->name,
                ]);

        $deductionnames =  Auth::user()
            ->deductionsname()
            ->paginate()
            ->transform(fn(DeductionName $deductionnames) => [
                'deduction_type_id' => $deductionnames->deduction_type_id,
                'deduction_name_id' => $deductionnames->id,
                'deduction_name' => $deductionnames->name,
                ]);
                
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

        $data=$request->all();

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
}
