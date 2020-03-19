<?php

use App\Designation;
use Illuminate\Database\Seeder;

class DesignationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $designations = [
            'HM' => 'HEAD MASTER',
            'PEO' => 'PRINCIPAL EXECUTIVE OFFICER',
            'DR' => 'DIRECTOR'
        ];

        for($i = 1; $i <= 7; $i++) {
            foreach ($designations as $code => $name) {
                factory(Designation::class)
                    ->create(['code' => $code, 'name' => $name, 'beneficiary_type_id' => $i]);
            }
        }
    }
}
