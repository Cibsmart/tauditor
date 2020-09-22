<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Allowance;
use App\Models\FixedValue;
use Inertia\Inertia;
use App\Models\AllowanceName;
use App\Models\PercentageValue;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class AllowancesController extends Controller
{
    public function __construct()
    {
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

        $allowances = Auth::user()->domain
            ->allowances()
            ->filters(request()->only('search'))
            ->paginate()
            ->transform(fn (Allowance $allowances) => [
                'id'             => $allowances->id,
                'name'           => $allowances->name(),
                'amount'         => $allowances->amount(),
                'value_type'     => $allowances->valueType(),
                'deduction_type' => $allowances->allowanceType()->name,
            ]);

        return Inertia::render('Allowances/Index', [
            'filters'    => $filters,
            'allowances' => $allowances,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Inertia\Response
     */
    public function create()
    {
        // $filters = Request::all('search');
        $allowances = Auth::user()->domain
            ->allowances()
            ->paginate()
            ->transform(fn (Allowance $allowances) => [
                'id'         => $allowances->id,
                'name'       => $allowances->name(),
                'amount'     => $allowances->amount(),
                'value_type' => $allowances->valueType(),
            ]);

        $allowance_names = AllowanceName::all();
        $fixed_values = FixedValue::all();
        $percentage_values = PercentageValue::all();
        // dd($fixed_value);

        return Inertia::render('Allowances/Create', [
            'allowances'        => $allowances,
            'allowance_names'   => $allowance_names,
            'fixed_values'      => $fixed_values,
            'percentage_values' => $percentage_values,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // dd(Request::all());

        $request->validate([
            'percentage'        => 'required|max:100|nullable',
            'allowance_name_id' => 'required',
            'percentage_type'   => 'required|nullable',
            'amount'            => 'required|nullable',
            'fixed_value'       => 'required|nullable',
        ]);
        try {
            $request = Request::all();
            //check the type of allowance
            if ($request['allowance_type'] == "percentage_type") {
                //create the percentage value
                $percentage_amount = $request['percentage'];

                $percentage_value = new PercentageValue();
                $percentage_value->percentage = $percentage_amount;
                $percentage_value->save();

                //then create the allowance
                $allowance = new Allowance();
                $allowance->allowance_name_id = $request['allowance_name_id'];
                $allowance->domain_id = 1;
                $allowance->valuable_type = "percentage_type";
                $allowance->valuable_id = $percentage_value->id;

                $allowance->save();

                return back()->with('success', "Percentage Type Allowance Created");
            } elseif ($request['allowance_type'] == "fixed_value") {
                //create the fixed values
                $fixed_amount = $request['amount'];
                $fixed_value = new FixedValue();
                $fixed_value->amount = $fixed_amount;
                $fixed_value->save();

                //then create the allowance
                $allowance = new Allowance();
                $allowance->allowance_name_id = $request['allowance_name_id'];
                $allowance->domain_id = 1;
                $allowance->valuable_type = "fixed_value";
                $allowance->valuable_id = $fixed_value->id;

                $allowance->save();

                return back()->with('success', "Fixed Value Allowance Created");
            } else {
                return back()->with('error', "Invalid Request");
            }
        } catch (Exception $e) {
            return back()->with('error', $e);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
