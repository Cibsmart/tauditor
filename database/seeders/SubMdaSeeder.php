<?php

namespace Database\Seeders;


use App\Models\Mda;
use App\Models\SubMda;
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
        $json = file_get_contents(storage_path() . '/json/sub_mda.json');

        //Convert json to an array
        $data = json_decode($json, true);

        foreach ($data as $beneficiary_type => $mdas) {
            foreach ($mdas as $mda => $depts) {
                $mda_id = $dbmdas->firstWhere('code', $mda)->id;

                foreach ($depts as $dept) {
                    $attributes = ['name' => $dept, 'mda_id' => $mda_id];

                    //SEC has Sub_Sub_MDAs, so we set the flag for those
                    $attributes = $mda === 'SEC'
                        ? array_merge($attributes, ['has_sub' => 1])
                        : $attributes;

                    factory(SubMda::class)->create($attributes);
                }
            }
        }
    }
}
