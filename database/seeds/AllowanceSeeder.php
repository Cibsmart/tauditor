<?php

use App\Models\Step;
use App\Models\Cadre;
use App\Models\Structure;
use App\Models\CadreStep;
use App\Models\Allowance;
use App\Models\GradeLevel;
use App\Models\FixedValue;
use App\Models\AllowanceName;
use App\Models\PercentageValue;
use Illuminate\Database\Seeder;

class AllowanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $steps = Step::all();
        $grades = GradeLevel::all();
        $allowance_names = AllowanceName::all();
        $salary_structures = Structure::all();

        //Get the content of of sub_mda.json
        $json = file_get_contents(storage_path().'/json/salary_structure.json');

        //Convert json to an array
        $data = json_decode($json, true);

        for ($i = 1; $i <= 2; $i++) {
            foreach ($data as $code => $structures) {
                $structure_id = $salary_structures->firstWhere('code', $code)->id;

                foreach ($structures as $items) {

                    $cadre_id = '';
                    $step_id = '';
                    $cadre_step_id = '';

                    foreach ($items as $key => $item) {
                        if (! in_array($key, ['GRADE', 'STEP', 'MBA'])) {
                            $allowance_name_id = $allowance_names->firstWhere('name', $key)->id;

                            $amount = $item <= 20
                                ? factory(PercentageValue::class)->create(['percentage' => $item])
                                : factory(FixedValue::class)->create(['amount' => $item]);

                            $allowance = $amount->allowance()->save(factory(Allowance::class)
                                ->make([
                                    'allowance_name_id' => $allowance_name_id,
                                    'domain_id'         => $i == 1 ? 'state' : 'jaac',
                                ]));

                            $allowance->cadreAllowance()->create(['cadre_step_id' => $cadre_step_id]);
                            continue;
                        }

                        if ($key == 'MBA') {
                            $cadre_step_id = factory(CadreStep::class)
                                ->create(['cadre_id'      => $cadre_id, 'step_id' => $step_id, 'monthly_basic' => $item,
                                ])->id;
                            continue;
                        }

                        if ($key == 'STEP') {
                            $st = str_pad($item, 2, '0', STR_PAD_LEFT);
                            $step_id = $steps->firstWhere('code', $st)->id;
                            continue;
                        }

                        if ($key == 'GRADE') {
                            $gd = str_pad($item, 2, '0', STR_PAD_LEFT);
                            $grade_id = $grades->firstWhere('code', $gd)->id;
                            $cadre = factory(Cadre::class)->raw([
                                'structure_id' => $structure_id, 'grade_level_id' => $grade_id,
                            ]);
                            $cadre_id = Cadre::firstOrCreate($cadre);
                            continue;
                        }
                    }
                }
            }
        }
    }
}
