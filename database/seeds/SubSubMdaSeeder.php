<?php

use App\SubMda;
use App\SubSubMda;
use Illuminate\Database\Seeder;

class SubSubMdaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $db_sub_mdas = SubMda::where('has_sub', 1)->get();

        //Get the content of of sub_mda.json
        $json = file_get_contents(storage_path().'/json/sub_sub_mda.json');

        //Convert json to an array
        $data = json_decode($json, true);

        foreach ($data as $mda => $sub_mdas) {
            foreach ($sub_mdas as $sub_mda => $depts) {
                $sub_mda_id = $db_sub_mdas->firstWhere('name', $sub_mda)->id;
                foreach ($depts as $dept) {
                    factory(SubSubMda::class)->create(['name' => $dept, 'sub_mda_id' => $sub_mda_id]);
                }
            }
        }
    }
}
