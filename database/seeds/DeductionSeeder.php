<?php

use App\Deduction;
use Illuminate\Database\Seeder;

class DeductionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for($j = 1; $j <= 2; $j++) {
            for($i = 1; $i <= 6; $i++) {
                factory(Deduction::class)->create([
                    'deduction_name_id' => ($i*$j),
                    'valuable_type' => 'valuable_type'.($i*$j),
                    'valuable_id' => $i.$j,
                    'domain_id' => $j,
                ]);
            }
        }
    }
}
