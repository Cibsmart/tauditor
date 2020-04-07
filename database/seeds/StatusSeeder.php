<?php

use App\Status;
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
            'TE' => 'TENURE ENDED',
            'AS' => 'APPOINTMENT SUSPENDED',
            'AT' => 'APPOINTMENT TERMINATED',
            'DD' => 'DECEASED',
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
