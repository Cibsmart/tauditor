<?php

namespace App\Http\Controllers;

use App\Deductible;
use App\Deduction;
use App\BeneficiaryType;
use App\Structure;
use App\Mda;
use App\Cadre;
use App\CadreStep;
use App\MdaStructure;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class DeductiblesController extends Controller
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
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  Deduction  $deduction
     * @return \Inertia\Response
     */
    public function create(Deduction $deduction)
    {
        $beneficiary_types = BeneficiaryType::all();

        $salary_structures = Structure::all();

        $mdas = Mda::all();

        $cadres = Cadre::all();

        $cadre_steps = CadreStep::all();

        return Inertia::render('Deductibles/Create', [
            'mdas' => $mdas,
            'cadres' => $cadres,
            'cadre_steps' => $cadre_steps,
            'deduction_id' => $deduction->id,
            'beneficiary_types' => $beneficiary_types,
            'salary_structures' => $salary_structures,
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
        $data = $request->all();

        $request->validate([
            'deductibleType' => ['required'],
            'deductionId'=>['required'],
        ]);


        $deduction_id = $data['deductionId'];

        if($data['deductibleType'] == "all" ){
            $deductible_id = Auth::user()->domain_id;
        }
        elseif($data['deductibleType'] == "beneficiary_type" ){
            $deductible_id = $data['beneficiaryType'];
            $request->validate([
                'deductibleType' => ['required'],
                'beneficiaryType' => ['required', 'integer'],
                'startDate' => ['nullable', 'date'],
                'endDate' => ['nullable', 'date'],
            ]);
        }
        elseif($data['deductibleType'] == "salary_structure" ){
            $deductible_id = $data['salaryStructure'];
            $request->validate([
                'deductibleType' => ['required'],
                'salaryStructure' => ['required', 'integer'],
                'startDate' => ['nullable', 'date'],
                'endDate' => ['nullable', 'date'],
            ]);
        }
        elseif($data['deductibleType'] == "cadre" ){
            $deductible_id = $data['Cadre'];
            $request->validate([
                'deductibleType' => ['required'],
                'Cadre' => ['required', 'integer'],
                'startDate' => ['nullable', 'date'],
                'endDate' => ['nullable', 'date'],
            ]);
        }
        elseif($data['deductibleType'] == "cadre_step" ){
            $deductible_id = $data['cadreStep'];
            $request->validate([
                'deductibleType' => ['required'],
                'Cadre' => ['required', 'integer'],
                'cadreStep' => ['required', 'integer'],
                'startDate' => ['nullable', 'date'],
                'endDate' => ['nullable', 'date'],
            ]);
        }
        elseif($data['deductibleType'] == "mda_structure" ){
            $request->validate([
                'deductibleType' => ['required'],
                'Mda' => ['required', 'integer'],
                'salaryStructure' => ['required', 'integer'],
                'startDate' => ['nullable', 'date'],
                'endDate' => ['nullable', 'date'],
            ]);

            $mda_structure = MdaStructure::where('mda_id', $data['Mda'])
                    ->where('structure_id', $data['salaryStructure'])
                    ->limit(1)
                    ->get();

            if($mda_structure->isEmpty()){
                $new_mda_structure = MdaStructure::create([
                        'mda_id'=>$data['Mda'],
                        'structure_id'=>$data['salaryStructure'],
                        ]);
                $deductible_id = $new_mda_structure->id;

            }
            else{
                $deductible_id = $mda_structure[0]->id;
            }

        }

        Deductible::create([
            'deductible_type'=>$data['deductibleType'],
            'deductible_id'=>$deductible_id,
            'deduction_id'=>$deduction_id,
            'start_date'=>$data['startDate'],
            'end_date'=>$data['endDate'],
            ]);


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
