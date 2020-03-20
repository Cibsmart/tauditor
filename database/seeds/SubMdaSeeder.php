<?php

use App\Mda;
use App\SubMda;
use Illuminate\Database\Seeder;

class SubMdaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $dbmdas = Mda::where('has_sub', 1)->get();

        //Get the content of of sub_mda.json
        $json =  file_get_contents(storage_path() .'/json/sub_mda.json');

        //Convert json to an array
        $data = json_decode($json, true);

        foreach($data as $beneficiary_type => $mdas) {
            foreach ($mdas as $mda => $depts) {
                $mda_id = $dbmdas->firstWhere('code', $mda)->id;
                foreach ($depts as $dept) {
                    factory(SubMda::class)->create(['name' => $dept, 'mda_id' => $mda_id]);
                }
            }
        }
    }
}
