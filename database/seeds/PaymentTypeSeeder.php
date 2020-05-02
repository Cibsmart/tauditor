<?php

use App\SalaryType;
use App\PaymentType;
use Illuminate\Database\Seeder;

class PaymentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $types = [
            'sal' => 'SALARY',
            'pen' => 'PENSION',
        ];

        foreach ($types as $key => $value) {
            factory(PaymentType::class)->create([
                'id' => $key,
                'name' => $value,
            ]);
        }
    }
}
