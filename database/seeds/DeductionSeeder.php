<?php

use App\Models\Deduction;
use App\Models\FixedValue;
use App\Models\DeductionName;
use App\Models\PercentageValue;
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
        $deduction_names = DeductionName::all();

        $deductions = [
            'TAX'     => 5,
            'AISHA'   => 5,
            'PENSION' => 2,
        ];

        for ($i = 1; $i <= 2; $i++) {
            foreach ($deductions as $name => $value) {
                $deduction_name_id = $deduction_names->firstWhere('name', $name)->id;

                //Create the value type
                $amount = $value <= 20
                    ? factory(PercentageValue::class)->create(['percentage' => $value])
                    : factory(FixedValue::class)->create(['amount' => $value]);

                //Create Allowance
                $amount->deduction()->save(factory(Deduction::class)
                    ->make([
                        'deduction_name_id' => $deduction_name_id,
                        'domain_id'         => $i == 1 ? 'state' : 'jaac',
                    ]));
            }
        }
    }
}
