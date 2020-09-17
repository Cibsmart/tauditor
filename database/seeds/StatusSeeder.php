<?php

use App\Models\Status;
use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $aStatus = [
            'NE' => 'NEW EMPLOYEE',
            'NP' => 'NEW PENSIONER',
            'RI' => 'RE-INSTATE',
        ];

        $iStatus = [
            'RT' => 'RETIRED',
            'RS' => 'RESIGNED',
            'SS' => 'SUSPENDED',
            'DD' => 'DECEASED',
            'SK' => 'SICK',
            'SL' => 'STUDY LEAVE',
            'AB' => 'ABSCONDED',
            'DM' => 'DISMISSED',
            'VR' => 'VOLUNTARY RETIREMENT',
            'LV' => 'LEAVE WITHOUT PAY',
            'EX' => 'EXCLUSION FROM PAYROLL',
        ];

        foreach ($aStatus as $key => $value) {
            factory(Status::class)->create([
                'code' => $key,
                'name' => $value,
                'state' => 0
            ]);
        }

        foreach ($iStatus as $key => $value) {
            factory(Status::class)->create([
                'code' => $key,
                'name' => $value,
                'state' => 1
            ]);
        }
    }
}
