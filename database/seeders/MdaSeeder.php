<?php

namespace Database\Seeders;


use App\Models\BeneficiaryType;
use App\Models\Mda;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MdaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $types = BeneficiaryType::all();

        //Get the content of of sub_mda.json
        $json = file_get_contents(storage_path() . '/json/mda.json');

        //Convert json to an array
        $data = json_decode($json, true);

        foreach ($data as $beneficiary_type => $mdas) {
            $beneficiary_type = Str::lower($beneficiary_type);

            foreach ($mdas as $mda) {
                $code = key($mda);
                $attributes = ['code' => $code, 'name' => $mda[$code], 'beneficiary_type_id' => $beneficiary_type];

                //LGEA, LGSC, SEC have Sub_MDAs, so we set the flag for those
                $attributes = in_array($beneficiary_type, ['lgea', 'lgsc']) || in_array($code, ['SEC'])
                    ? array_merge($attributes, ['has_sub' => 1])
                    : $attributes;

                factory(Mda::class)->create($attributes);
            }
        }
    }
}
