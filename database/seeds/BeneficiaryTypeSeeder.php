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
            'cv' => 'ANAMBRA STATE CIVIL SERVANT',
            'sco' => 'STATUTORY COMMISSION AND OFFICERS',
            'pa' => 'POLITICAL APPOINTEES',
            'anpen' => 'ANAMBRA STATE GOVERNMENT PENSIONERS',
        ];

        $staff_types_jaac = [
            'lgea' => 'LOCAL GOVERNMENT EDUCATION AUTHORITY',
            'lgsc' => 'LOCAL GOVERNMENT SERVICE COMMISSION',
            'lgpen' => 'LOCAL GOVERNMENT PENSIONERS',
        ];

        foreach($staff_types_state as $code => $name){
            $attributes = [ 'id' => $code, 'name' => $name, 'domain_id' => 'state' ];

            $attributes = $code == 'ANPEN' ? array_merge($attributes, ['pensioners' => 1]) : $attributes;

            factory(BeneficiaryType::class)
                ->create($attributes);
        }

        foreach($staff_types_jaac as $code => $name){
            $attributes = [ 'id' => $code, 'name' => $name, 'domain_id' => 'jaac' ];

            $attributes = $code == 'LGPEN' ? array_merge($attributes, ['pensioners' => 1]) : $attributes;

            factory(BeneficiaryType::class)
                ->create($attributes);
        }
    }
}
