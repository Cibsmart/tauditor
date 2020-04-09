<?php

namespace App\Http\Controllers;

use App\Deduction;
use App\FixedValue;
use Inertia\Inertia;
use App\ComputedValue;
use App\DeductionType;
use App\DeductionName;
use App\PercentageValue;
use Illuminate\Support\Str;
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
            'deduction_type' => ['required', 'integer'],
            'deduction_name' => ['required', 'string'],
            'value_type' => ['required', 'string'],
            'amount' => ['nullable', 'numeric', 'positive'],
        ]);

        $deduction_type_id = $request->deduction_type;
        $deduction_name = $request->deduction_name;
        $value_code = $request->value_type;
        $amount = $request->amount;

        $deduction_name = $this->deductionName($deduction_name, $deduction_type_id);

        $valuable = $this->valueType($value_code, $amount, $deduction_name);

        $valuable->deduction()->create([
            'deduction_name_id' => $deduction_name->id,
            'domain_id' => Auth::user()->domain->id,
            'valuable_type' => $valuable
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
     * @param $value_code
     * @param $amount
     * @param $deduction_name
     * @return mixed
     */
    private function valueType($value_code, $amount, $deduction_name)
    {
        if ($value_code == 'fixed') {
            return FixedValue::create(['amount' => $amount]);
        }

        if ($value_code == 'percentage') {
            return PercentageValue::create(['percentage' => $amount]);
        }

        if ($value_code == 'computed') {
            $computer = 'compute_'.$deduction_name;
            return ComputedValue::create(['computer' => $computer]);
        }
    }

    /**
     * @param $deduction_name
     * @param $deduction_type_id
     * @return mixed
     */
    private function deductionName($deduction_name, $deduction_type_id)
    {
        $deduction_name = Auth::user()->domain->deductionNames()
                                              ->where('name', $deduction_name)
                                              ->first()
            ?? Auth::user()->domain->deductionNames()
                                   ->create([
                                       'deduction_type_id' => $deduction_type_id,
                                       'code' => $deduction_name,
                                       'name' => $deduction_name,
                                   ]);
        return $deduction_name;
    }
}
