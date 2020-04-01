<?php

namespace App\Http\Controllers;

// use Illuminate\Http\Request;

use App\Deduction;
use App\Beneficiary;
use Inertia\Inertia;
use Inertia\Response;
use App\StructuredSalary;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class DeductionsController extends Controller
{

    public function __construct(){
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $filters = Request::all('search');
        $deductions = Auth::user()->domain
        ->deductions()
        ->with($this->relationships())
        ->filters(Request::only('search'))
        ->paginate()
        ->transform(fn (Deduction $deductions) => [
            'id' => $deductions->id,
            'name' => $deductions->name,
            'valuable_type' => $deductions->valuable_type,
            'code' => $deductions->code(),
            'domain' => $deductions->domain(),
            'amount' => $deductions->amount(),
            
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
