<?php

use App\Structure;
use App\AllowanceType;
use Illuminate\Database\Seeder;

class AllowanceTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $allowance_types = [
            'HOUSING' => 'HOUSING',
            'MEAL' => 'MEAL',
            'TRANSPORT' => 'TRANSPORT',
            'UTILITY' => 'UTILITY',
            'OTHERS' => 'OTHER ALLOWANCES'
        ];

        for($i = 1; $i <= 2; $i++) {
            foreach ($allowance_types as $code => $name) {
                factory(AllowanceType::class)->create(['code' => $code, 'name' => $name, 'domain_id' => $i]);
            }
        }
    }
}
