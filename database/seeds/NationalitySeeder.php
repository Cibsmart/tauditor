<?php

namespace Database\Seeders;

use App\Models\Nationality;
use Illuminate\Database\Seeder;

class NationalitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Nationality::create(['id' => 'NG', 'name' => 'NIGERIA']);
        Nationality::create(['id' => 'ML', 'name' => 'MALI']);
        Nationality::create(['id' => 'JM', 'name' => 'JAMAICA']);
    }
}
