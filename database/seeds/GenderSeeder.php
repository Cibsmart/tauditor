<?php

use App\Gender;
use Illuminate\Database\Seeder;

class GenderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Gender::class)->create([
          'code' => 'M',
          'name' => 'Male'
        ]);

        factory(Gender::class)->create([
          'code' => 'F',
          'name' => 'Female'
        ]);
    }
}
