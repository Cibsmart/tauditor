<?php

namespace Database\Seeders;


use App\Models\Bank;
use Illuminate\Database\Seeder;

class BankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $banks = [
            '044' => 'ACCESS BANK PLC',
            '063' => 'ACCESS BANK PLC (DIAMOND)',
            '023' => 'CITIBANK',
            '050' => 'ECOBANK NIGERIA PLC',
            '084' => 'ENTERPRISE BANK PLC',
            '070' => 'FIDELITY BANK PLC',
            '011' => 'FIRST BANK OF NIGERIA PLC',
            '214' => 'FIRST CITY MONUMENT BANK PLC',
            '058' => 'GUARANTY TRUST BANK PLC',
            '030' => 'HERITAGE BANK LIMITED',
            '301' => 'JAIZ BANK PLC',
            '082' => 'KEYSTONE BANK PLC',
            '014' => 'MAINSTREET BANK PLC',
            '076' => 'SKYE BANK PLC',
            '039' => 'STANBIC-IBTC BANK PLC',
            '232' => 'STERLING BANK PLC',
            '032' => 'UNION BANK OF NIGERIA PLC',
            '033' => 'UNITED BANK FOR AFRICA PLC',
            '215' => 'UNITY BANK PLC',
            '035' => 'WEMA BANK PLC',
            '057' => 'ZENITH BANK PLC',
        ];

        foreach ($banks as $key => $value) {
            factory(Bank::class)->create([
                'code' => $key,
                'name' => $value,
            ]);
        }
    }
}
