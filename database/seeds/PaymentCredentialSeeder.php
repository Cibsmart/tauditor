<?php

use App\Bank;
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
        $banks = Bank::all();

        factory(PaymentCredential::class)->create([
            'payment_type' => 'sal',
            'terminal_id' => '',
            'account_number' => '',
            'account_type' => '10',
            'bank_id' => $banks->where('code', '070')->first()->id,
            'domain_id' => 1,
        ]);

        factory(PaymentCredential::class)->create([
            'payment_type' => 'pen',
            'terminal_id' => '',
            'account_number' => '',
            'account_type' => '10',
            'bank_id' => $banks->where('code', '070')->first()->id,
            'domain_id' => 1,
        ]);

        factory(PaymentCredential::class)->create([
            'payment_type' => 'leave',
            'terminal_id' => '',
            'account_number' => '',
            'account_type' => '10',
            'bank_id' => $banks->where('code', '070')->first()->id,
            'domain_id' => 1,
        ]);

        factory(PaymentCredential::class)->create([
            'payment_type' => 'sal',
            'terminal_id' => '',
            'account_number' => '',
            'account_type' => '10',
            'bank_id' => $banks->where('code', '070')->first()->id,
            'domain_id' => 2,
        ]);

        factory(PaymentCredential::class)->create([
            'payment_type' => 'pen',
            'terminal_id' => '',
            'account_number' => '',
            'account_type' => '10',
            'bank_id' => $banks->where('code', '070')->first()->id,
            'domain_id' => 2,
        ]);

        factory(PaymentCredential::class)->create([
            'payment_type' => 'leave',
            'terminal_id' => '',
            'account_number' => '',
            'account_type' => '10',
            'bank_id' => $banks->where('code', '070')->first()->id,
            'domain_id' => 2,
        ]);
    }
}
