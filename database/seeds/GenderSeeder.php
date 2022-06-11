<?php

use App\Models\Gender;
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
            'id' => 'M',
            'name' => 'MALE',
        ]);

        factory(Gender::class)->create([
            'id' => 'F',
            'name' => 'FEMALE',
        ]);
    }
}
