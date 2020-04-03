<?php

use App\DeductionName;
use Illuminate\Database\Seeder;

class DeductionNameSeeder extends Seeder
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
                factory(DeductionName::class)->create([
                    'code' => 'deduction'.$i.$j,
                    'name' => 'deduction'.$i.$j,
                    'deduction_type_id' => ($i*$j),
                ]);
            }
        }
    }
}
