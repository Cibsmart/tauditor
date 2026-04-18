<?php

namespace Database\Seeders;


use App\Models\LocalGovernment;
use App\Models\State;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class StateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Get the content of of sub_mda.json
        $json = file_get_contents(storage_path() . '/json/state_lga.json');

        //Convert json to an array
        $data = json_decode($json, true);

        foreach ($data as $states) {
            $state = Str::of($states['states']['name'])->upper()->replace('STATE', '')->trim();
            $state_id = factory(State::class)->create(['name' => $state])->id;

            $lgas = $states['states']['locals'];

            foreach ($lgas as $lga) {
                $lga = Str::of($lga['name'])->upper()->trim();
                factory(LocalGovernment::class)->create(['name' => $lga, 'state_id' => $state_id]);
            }
        }
    }
}
