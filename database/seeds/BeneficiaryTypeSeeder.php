<?php

use App\BeneficiaryType;
use Illuminate\Database\Seeder;

class BeneficiaryTypeSeeder extends Seeder
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
            'ANPEN' => 'ANAMBRA STATE GOVERNMENT PENSIONERS',
        ];

        $staff_types_jaac = [
            'LGEA' => 'LOCAL GOVERNMENT EDUCATION AUTHORITY',
            'LGSC' => 'LOCAL GOVERNMENT SERVICE COMMISSION',
            'LGPEN' => 'LOCAL GOVERNMENT PENSIONERS',
        ];

        foreach($staff_types_state as $code => $name){
            $attributes = [ 'code' => $code, 'name' => $name, 'domain_id' => 'state' ];

            $attributes = $code == 'ANPEN' ? array_merge($attributes, ['pensioners' => 1]) : $attributes;

            factory(BeneficiaryType::class)
                ->create($attributes);
        }

        foreach($staff_types_jaac as $code => $name){
            $attributes = [ 'code' => $code, 'name' => $name, 'domain_id' => 'jaac' ];

            $attributes = $code == 'LGPEN' ? array_merge($attributes, ['pensioners' => 1]) : $attributes;

            factory(BeneficiaryType::class)
                ->create($attributes);
        }
    }
}
