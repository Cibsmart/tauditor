<?php

use App\Step;
use App\Cadre;
use App\Structure;
use App\CadreStep;
use App\GradeLevel;
use Illuminate\Support\Str;
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
        $salary_structures = Structure::all();
        $grades = GradeLevel::all();
        $steps = Step::all();

        //Get the content of of sub_mda.json
        $json =  file_get_contents(storage_path() .'/json/salary_structure.json');

        //Convert json to an array
        $data = json_decode($json, true);

        foreach ($data as $code => $structures) {
            $structure_id = $salary_structures->firstWhere('code', $code)->id;

            foreach ($structures as $items) {

                $cadre_id = '';
                $grade_id = '';
                $step_id = '';

                foreach ($items as $key => $item) {
                    if(! in_array($key, ['GRADE', 'STEP', 'MBA'])){
                        continue;
                    }

                    if($key == 'MBA'){
                        factory(CadreStep::class)
                            ->create(['cadre_id' => $cadre_id, 'step_id' => $step_id, 'monthly_basic' => $item]);
                        continue;
                    }

                    if($key == 'STEP'){
                        $st = str_pad($item, 2, '0', STR_PAD_LEFT);
                        $step_id = $steps->firstWhere('code', $st)->id;
                        continue;
                    }

                    if($key == 'GRADE'){
                        $gd = str_pad($item, 2, '0', STR_PAD_LEFT);
                        $grade_id = $steps->firstWhere('code', $gd)->id;
                        $cadre = factory(Cadre::class)->raw(['structure_id' => $structure_id, 'grade_level_id' => $grade_id]);
                        $cadre_id = Cadre::firstOrCreate($cadre);
                        continue;
                    }
                }
            }
        }
    }
}
