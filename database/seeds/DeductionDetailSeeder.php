<?php

use App\DeductionDetail;
use Illuminate\Database\Seeder;

class DeductionDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for($j = 1; $j <= 2; $j++) {
            for($i = 1; $i <= 24; $i++) {
                factory(DeductionDetail::class)->create([
                    'deduction_id' => $i,
                    'amount' => $j.$i,
                    'beneficiary_id' => $i,
                ]);
            }
        }
    }
}
