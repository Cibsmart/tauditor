<?php

use App\Bank;
use App\PayComm;
use Illuminate\Database\Seeder;

class PayCommSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $banks = Bank::all();

        for ($i = 1; $i <= 2; $i++) {
            factory(PayComm::class)->create([
                'code' => 'PayComm I',
                'name' => 'Fidelity Bank Plc',
                'account_number' => $i == 1 ? '5030101784' : '5030101784', //TODO Update account Number for State Domain = 1
                'commission' => 120,
                'bankable_type' => 'commercial',
                'bankable_id' => $banks->where('code', '070')->first()->id,
                'domain_id' => $i
            ]);

            factory(PayComm::class)->create([
                'code' => 'PayComm II',
                'name' => 'Tenece Professional Services',
                'account_number' => '4010478742',
                'commission' => 240,
                'bankable_type' => 'commercial',
                'bankable_id' => $banks->where('code', '070')->first()->id,
                'domain_id' => $i
            ]);
        }
    }
}
