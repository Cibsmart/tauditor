<?php

use App\PaymentType;
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
        $payment_types = PaymentType::all();

        $staff_types_state = [
            'cv'    => 'ANAMBRA STATE CIVIL SERVANT',
            'sco'   => 'STATUTORY COMMISSION AND OFFICERS',
            'pa'    => 'POLITICAL APPOINTEES',
            'anpen' => 'ANAMBRA STATE GOVERNMENT PENSIONERS',
        ];

        $staff_types_jaac = [
            'lgea'  => 'LOCAL GOVERNMENT EDUCATION AUTHORITY',
            'lgsc'  => 'LOCAL GOVERNMENT SERVICE COMMISSION',
            'lgpen' => 'LOCAL GOVERNMENT PENSIONERS',
        ];

        foreach ($staff_types_state as $code => $name) {
            $attributes = ['id' => $code, 'name' => $name, 'domain_id' => 'state'];

            if ($code === 'anpen') {
                $attributes = array_merge($attributes, ['pensioners' => 1]);
                $beneficiary_type = factory(BeneficiaryType::class)->create($attributes);
                $beneficiary_type->paymentTypes()->attach($payment_types->firstWhere('id', 'pen'));
                continue;
            }

            $beneficiary_type = factory(BeneficiaryType::class)->create($attributes);
            $beneficiary_type->paymentTypes()->attach($payment_types->where('id', 'sal'));
        }

        foreach ($staff_types_jaac as $code => $name) {
            $attributes = ['id' => $code, 'name' => $name, 'domain_id' => 'jaac'];

            if ($code === 'lgpen') {
                $attributes = array_merge($attributes, ['pensioners' => 1]);
                $beneficiary_type = factory(BeneficiaryType::class)->create($attributes);
                $beneficiary_type->paymentTypes()->attach($payment_types->firstWhere('id', 'pen'));
                continue;
            }

            $beneficiary_type = factory(BeneficiaryType::class)->create($attributes);
            $beneficiary_type->paymentTypes()->attach($payment_types->where('id', 'sal'));
        }
    }
}
