<?php

use App\StaffType;
use Illuminate\Database\Seeder;

class StaffTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $staff_types_state = [
            'CV' => 'ANAMBRA STATE CIVIL SERVANT',
            'SCO' => 'STATUTORY COMMISSION AND OFFICERS',
            'PA' => 'POLITICAL APPOINTEES',
            'PEN' => 'ANAMBRA STATE GOVERNMENT PENSIONERS',
        ];

        $staff_types_jaac = [
            'LGEA' => 'LOCAL GOVERNMENT EDUCATION AUTHORITY',
            'LGSC' => 'LOCAL GOVERNMENT SERVICE COMMISSION',
            'PEN' => 'LOCAL GOVERNMENT PENSIONERS',
        ];

        foreach($staff_types_state as $code => $name){
            factory(StaffType::class)
                ->create([ 'code' => $code, 'name' => $name, 'domain_id' => 1 ]);
        }

        foreach($staff_types_jaac as $code => $name){
            factory(StaffType::class)
                ->create([ 'code' => $code, 'name' => $name, 'domain_id' => 2 ]);
        }
    }
}
