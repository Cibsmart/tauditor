<?php

namespace App\Http\Controllers;

use App\Allowance;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;


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
        $filters = Request::all('search');

        $allowances = Auth::user()->domain
            ->allowances()
            ->filters(Request::only('search'))
            ->paginate()
            ->transform(fn(Allowance $allowances) => [
                'id' => $allowances->id,
                'name' => $allowances->name(),
                'amount' => $allowances->amount(),
                'value_type' => $allowances->valueType(),
                'deduction_type' => $allowances->allowanceType()->name,
            ]);

        return Inertia::render('Allowances/Index', [
            'filters' => $filters,
            'allowances' => $allowances
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
