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

        $others = [
            'TAX' => 'TAX',
            'AISHA' => 'AISHA',
            'PENSION' => 'PENSION',
        ];

        for($i = 1; $i <= 2; $i++) {
            foreach ($others as $code => $name) {
                factory(DeductionName::class)
                    ->create([
                        'code' => $code,
                        'name' => $name,
                        'deduction_type_id' => 1
                    ]);
            }
        }
    }
}
