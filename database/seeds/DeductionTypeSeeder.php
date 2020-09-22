<?php

use App\Models\DeductionType;
use Illuminate\Database\Seeder;

class DeductionTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $deduction_types = [
            'STATUTORY' => 'STATUTORY',
            'DUES'      => 'DUES',
            'LOANS'     => 'LOANS',
        ];

        for ($i = 1; $i <= 2; $i++) {
            foreach ($deduction_types as $code => $name) {
                factory(DeductionType::class)->create([
                    'code'      => $code,
                    'name'      => $name,
                    'domain_id' => $i == 1 ? 'state' : 'jaac',
                ]);
            }
        }
    }
}
