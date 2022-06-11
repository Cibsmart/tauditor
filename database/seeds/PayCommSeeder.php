<?php

use App\Models\Bank;
use App\Models\PayComm;
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

        //FIDELITY STATE
        factory(PayComm::class)->create([
            'code' => 'PayComm I',
            'name' => 'FIDELITY BANK PLC',
            'account_number' => '5030112362',
            'commission' => 120,
            'bankable_type' => 'commercial',
            'bankable_id' => $banks->where('code', '070')->first()->id,
            'domain_id' => 'state',
        ]);

        //FIDELITY JAAC
        factory(PayComm::class)->create([
            'code' => 'PayComm I',
            'name' => 'FIDELITY BANK PLC',
            'account_number' => '5030101784',
            'commission' => 120,
            'bankable_type' => 'commercial',
            'bankable_id' => $banks->where('code', '070')->first()->id,
            'domain_id' => 'jaac',
        ]);

        //TENECE STATE
        factory(PayComm::class)->create([
            'code' => 'PayComm II',
            'name' => 'TENECE PROFESSIONAL SERVICES',
            'account_number' => '4010478742',
            'commission' => 243.87,
            'bankable_type' => 'commercial',
            'bankable_id' => $banks->where('code', '070')->first()->id,
            'domain_id' => 'state',
        ]);

        //TENECE JAAC
        factory(PayComm::class)->create([
            'code' => 'PayComm II',
            'name' => 'TENECE PROFESSIONAL SERVICES',
            'account_number' => '4010478742',
            'commission' => 243.87,
            'bankable_type' => 'commercial',
            'bankable_id' => $banks->where('code', '070')->first()->id,
            'domain_id' => 'jaac',
        ]);
    }
}
