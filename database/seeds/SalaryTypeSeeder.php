<?php

use App\ValueType;
use App\SalaryType;
use Illuminate\Database\Seeder;

class SalaryTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $types = [
            'personalized' => 'PERSONALIZED SALARY',
            'structured' => 'STRUCTURED SALARY',
        ];

        foreach ($types as $key => $value) {
            factory(SalaryType::class)->create([
                'id' => $key,
                'name' => $value,
            ]);
        }
    }
}
