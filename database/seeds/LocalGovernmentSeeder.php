<?php

use App\LocalGovernment;
use Illuminate\Database\Seeder;

class LocalGovernmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $lgas = [
          'AB' => 'Abia',
          'UM' => 'Umuahia',
        ];

        foreach ($lgas as $key => $value) {
            factory(LocalGovernment::class)->create([
              'code' => $key,
              'name' => $value,
            ]);
        }
    }
}
