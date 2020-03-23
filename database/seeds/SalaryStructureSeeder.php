<?php

use App\Structure;
use App\SalaryStructure;
use Illuminate\Database\Seeder;
use Faker\Generator as Faker;

class SalaryStructureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Faker $faker)
    {
        $structures = Structure::all();

        foreach ($structures as $structure) {
            $grades = 17; //$faker->numberBetween(1, 17);
            $steps = 15; //$faker->numberBetween(1, 15);

            for($i = 1; $i <= $grades; $i++){
                for($j = 1; $j <= $steps; $j++){
                    $f = factory(SalaryStructure::class)
                        ->create(['structure_id' => fn() => $structure->id, 'grade_level_id' => $i, 'step_id' => $j]);
                }
            }
        }
    }
}
