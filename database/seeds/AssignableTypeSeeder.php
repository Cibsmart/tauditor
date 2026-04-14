<?php

use App\Models\AssignableType;
use Illuminate\Database\Seeder;

class AssignableTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $types = [
            'all'              => 'EVERY ONE INCLUDING PENSIONERS',
            'beneficiary_type' => 'BENEFICIARY TYPE',
            'salary_structure' => 'SALARY STRUCTURE',
            'direct'           => 'MDA STRUCTURE',
            'mda_structure'    => 'MDA STRUCTURE',
            'beneficiary'      => 'BENEFICIARY',
        ];

        foreach ($types as $key => $value) {
            factory(AssignableType::class)->create([
                'id'   => $key,
                'name' => $value,
            ]);
        }
    }
}
