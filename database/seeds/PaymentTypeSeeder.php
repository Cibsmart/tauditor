<?php

use App\Models\PaymentType;
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
        $payment_types = [
            'main' => [
                'sal' => 'SALARY',
                'pen' => 'PENSION',
                'lev' => 'ANNUAL LEAVE ALLOWANCE'
              ],
            'other' => [
                'nys' => 'NYSC',
                'all' => 'ALLOWANCE',
                'arr' => 'ARREARS'
            ]
        ];

        foreach ($payment_types as $category => $types) {
            foreach ($types as $key => $value) {
                factory(PaymentType::class)->create([
                    'id'       => $key,
                    'category' => $category,
                    'name'     => $value,
                ]);
            }
        }
    }
}
