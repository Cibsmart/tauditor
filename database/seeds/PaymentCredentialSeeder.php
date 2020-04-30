<?php

use App\Bank;
use App\BeneficiaryType;
use App\PaymentCredential;
use Illuminate\Database\Seeder;

class PaymentCredentialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $bank = Bank::where('code', '070')->first()->id;

        /**
         * STATE
         */
        //Civil Servants
        factory(PaymentCredential::class)->create([
            'payment_type_id' => 'sal',
            'terminal_id' => '',
            'account_number' => '5030101791',
            'account_name' => 'ANSG-SALARY ADMINISTRATION ACCOUNT 2',
            'pan' => '6280510107035825320',
            'account_type' => '10',
            'bank_id' => $bank,
            'beneficiary_type_id' => 'cv',
            'domain_id' => 'state',
        ]);

        //Statutory Commission and Officers
        factory(PaymentCredential::class)->create([
            'payment_type_id' => 'sal',
            'terminal_id' => '',
            'account_number' => '5030101791',
            'account_name' => 'ANSG-SALARY ADMINISTRATION ACCOUNT 2',
            'pan' => '6280510107035825320',
            'account_type' => '10',
            'bank_id' => $bank,
            'beneficiary_type_id' => 'cv',
            'domain_id' => 'state',
        ]);

        //political appointees
        factory(PaymentCredential::class)->create([
            'payment_type_id' => 'sal',
            'terminal_id' => '',
            'account_number' => '5030101791',
            'account_name' => 'ANSG-SALARY ADMINISTRATION ACCOUNT 2',
            'pan' => '6280510107035825320',
            'account_type' => '10',
            'bank_id' => $bank,
            'beneficiary_type_id' => 'cv',
            'domain_id' => 'state',
        ]);

        //Anambra State Government Pensioners
        factory(PaymentCredential::class)->create([
            'payment_type_id' => 'pen',
            'terminal_id' => '',
            'account_number' => '5030101801',
            'account_name' => 'ANSG-Pension Administration Account 2',
            'pan' => '6280510107035825379',
            'account_type' => '10',
            'bank_id' => $bank,
            'beneficiary_type_id' => 'anpen',
            'domain_id' => 'state',
        ]);


        /**
         * JAAC
         */
        //Local Government Education Authority
        factory(PaymentCredential::class)->create([
            'payment_type_id' => 'sal',
            'terminal_id' => '',
            'account_number' => '5030090220',
            'account_name' => 'ANS-LGEA SALARY ADMIN1-TEACHERS',
            'pan' => '6280510107035528080',
            'account_type' => '10',
            'bank_id' => $bank,
            'beneficiary_type_id' => 'lgea',
            'domain_id' => 'jaac',
        ]);

        //Local Government Service Commission
        factory(PaymentCredential::class)->create([
            'payment_type_id' => 'sal',
            'terminal_id' => '',
            'account_number' => '5030090237',
            'account_name' => 'ANS-LGA SALARY ADMIN2-LGA WORKERS',
            'pan' => '6280510107035528106',
            'account_type' => '10',
            'bank_id' => $bank,
            'beneficiary_type_id' => 'lgsc',
            'domain_id' => 'jaac',
        ]);

        // Anambra State Local Government Pensioners
        factory(PaymentCredential::class)->create([
            'payment_type_id' => 'pen',
            'terminal_id' => '',
            'account_number' => '5030090244',
            'account_name' => 'ANS-LGA PENSION ADMIN SALARY',
            'pan' => '6280510107035528114',
            'account_type' => '10',
            'bank_id' => $bank,
            'beneficiary_type_id' => 'lgpen',
            'domain_id' => 'jaac',
        ]);
    }
}
