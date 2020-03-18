<?php

use App\State;
use Illuminate\Database\Seeder;

class StateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $states = [
        'AB' => 'Abia',
        'AN' => 'Anambra',
        'EN' => 'Enugu',
        'EB' => 'Ebonyi',
        'IM' => 'Imo',
      ];

        foreach ($states as $key => $value) {
            factory(State::class)->create([
              'code' => $key,
              'name' => $value,
            ]);
        }
    }
}
