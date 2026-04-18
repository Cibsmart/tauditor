<?php

namespace Database\Seeders;


use App\Models\Bank;
use App\Models\Domain;
use App\Models\MicroFinanceBank;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

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

        $json = file_get_contents(storage_path() . '/json/mfb.json');

        $data = json_decode($json, true);

        foreach ($data as $domain => $mfbs) {
            foreach ($mfbs as $mfb) {
                $bank_id = $banks->firstWhere('code', $mfb['code'])->id;
                factory(MicroFinanceBank::class)->create([
                    'name' => Str::of($mfb['name'])->trim(),
                    'account_number' => Str::of($mfb['account_number'])->trim(),
                    'bank_id' => $bank_id,
                    'domain_id' => $domain,
                ]);
            }
        }
    }
}
