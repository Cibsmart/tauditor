<?php

namespace App\Http\Controllers;

use App\Deduction;
use App\ValueType;
use App\FixedValue;
use Inertia\Inertia;
use App\ComputedValue;
use App\PercentageValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Validator;
use App\Http\Requests\DeductionRequest;

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
        $value_types = ValueType::all();
        $deduction_types =  Auth::user()->deductionTypes()->get();
        $deduction_names =  Auth::user()->deductionNames()->get();

        return Inertia::render('Deductions/Create', [
            'value_types' => $value_types,
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
    public function store(DeductionRequest $request)
    {
        $deduction_name = $this->deductionName($request->deduction_type, $request->deduction_name, $request->new_deduction);

        $valuable = $this->valueType($request->value_type, $request->value, $deduction_name->name);

        $valuable->deduction()->create([
            'deduction_name_id' => $deduction_name->id,
            'domain_id' => Auth::user()->domain->id,
        ]);

        return redirect()->back()->withSuccess('Deduction Created Successfully');
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

    private function relationships()
    {
        // Relationships Eager Loaded with Beneficiaries
        // to avoid multiple Database Round Trip
        return [
            'deductionDetails.deduction',
            'deductionName.deductionType',
        ];
    }

    /**
     * @param $value_type
     * @param $amount
     * @param $deduction_name
     * @return mixed
     */
    private function valueType($value_type, $amount, $deduction_name)
    {
        $value_type = ValueType::find($value_type)->id;

        if ($value_type == 'fixed') {
            return FixedValue::create(['amount' => $amount]);
        }

        if ($value_type == 'percentage') {
            return PercentageValue::create(['percentage' => $amount]);
        }

        if ($value_type == 'computed') {
            $computer = 'compute_'.$deduction_name;
            return ComputedValue::create(['computer' => $computer]);
        }
    }

    /**
     * @param $deduction_type_id
     * @param $deduction_name_id
     * @param $deduction_name
     * @return mixed
     */
    private function deductionName($deduction_type_id, $deduction_name_id, $deduction_name = null)
    {
        if($deduction_name_id > 0){
            return Auth::user()->domain->deductionNames()->find($deduction_name_id);
        }

        return Auth::user()->domain->deductionNames()->create([
            'deduction_type_id' => $deduction_type_id,
            'code' => $deduction_name,
            'name' => $deduction_name,
        ]);
    }
}
