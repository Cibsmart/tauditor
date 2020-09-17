<?php

use App\Models\BankType;
use App\Models\QualificationType;
use Illuminate\Database\Seeder;

class BankTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $types = [
            'commercial' => 'COMMERCIAL BANK',
            'micro_finance' => 'MICROFINANCE BANK',
        ];

        foreach ($types as $code => $name) {
            factory(BankType::class)->create([
                'id' => $code,
                'name' => $name,
            ]);
        }
    }
}
