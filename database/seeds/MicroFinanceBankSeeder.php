<?php

use App\Bank;
use App\MicroFinanceBank;
use Illuminate\Database\Seeder;

class MicroFinanceBankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $banks = Bank::all();

        $mfbs = [
            'ADAZI-ANI MICRO FINANCE BANK',
            'EQUINOX MICRO FINANCE BANK',
            'AWKA-ETITI MICRO FINANCE BANK',
            'IGBOUKWU MICRO FINANCE BANK',
            'NNOKWA MICRO FINANCE BANK',
        ];

        foreach ($mfbs as $mfb) {
            factory(MicroFinanceBank::class)->create([
                'name' => $mfb,
                'bank_id' => $banks->random()->id,
                'domain_id' => 1,
            ]);
        }

        foreach ($mfbs as $mfb) {
            factory(MicroFinanceBank::class)->create([
                'name' => $mfb,
                'domain_id' => 2
            ]);
        }
    }
}
