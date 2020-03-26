<?php

use App\Structure;
use App\AllowanceName;
use App\AllowanceType;
use Illuminate\Database\Seeder;

class AllowanceNameSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $allowance_types = AllowanceType::all();

        $others = [
            'CD' => 'CALL DUTY',
            'HZ' => 'HAZARD',
            'INDUCE' => 'INDUCEMENT',
        ];

        for($i = 1; $i <= 2; $i++) {
            foreach ($others as $code => $name) {
                factory(AllowanceName::class)
                    ->create([
                        'code' => $code,
                        'name' => $name,
                        'allowance_type_id' => $allowance_types
                            ->where('domain_id', $i)
                            ->where('code', 'OTHERS')->first()->id
                    ]);
            }
        }
    }
}
