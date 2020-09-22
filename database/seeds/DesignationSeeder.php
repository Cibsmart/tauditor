<?php

use App\Models\Designation;
use App\Models\BeneficiaryType;
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
        $beneficiary_types = BeneficiaryType::all();

        $designations = [
            'HM'  => 'HEAD MASTER',
            'PEO' => 'PRINCIPAL EXECUTIVE OFFICER',
            'DR'  => 'DIRECTOR',
            'PEN' => 'PENSIONER',
        ];

        foreach ($beneficiary_types as $beneficiary_type) {
            foreach ($designations as $code => $name) {
                factory(Designation::class)
                    ->create(['code' => $code, 'name' => $name, 'beneficiary_type_id' => $beneficiary_type->id]);
            }
        }
    }
}
