<?php

use App\Structure;
use Illuminate\Database\Seeder;

class StructureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $structures = [
            'CONMESS' => 'CONSOLIDATED MEDICAL SALARY STRUCTURE',
            'CONHESS' => 'CONSOLIDATED HEALTH SALARY STRUCTURE',
            'CONTISS' => 'CONSOLIDATED CIVIL SERVANT SALARY STRUCTURE',
        ];

        for($i = 1; $i <= 2; $i++) {
            foreach ($structures as $code => $name) {
                factory(Structure::class)->create(['code' => $code, 'name' => $name, 'domain_id' => $i]);
            }
        }
    }
}
