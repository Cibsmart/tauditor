<?php

use App\Bank;
use App\Domain;
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
        $domains = Domain::all();
        $banks = Bank::all();

        $json =  file_get_contents(storage_path() .'/json/mfb.json');

        $data = json_decode($json, true);

        foreach ($data as $domain => $mfbs) {
            $domain_id = $domains->firstWhere('code', $domain)->id;
            foreach ($mfbs as $mfb){
                $bank_id = $banks->firstWhere('code', $mfb['code'])->id;
                factory(MicroFinanceBank::class)->create([
                    'name' => $mfb['name'],
                    'account_number' => $mfb['account_number'],
                    'bank_id' => $bank_id,
                    'domain_id' => $domain_id,
                ]);
            }
        }
    }
}
