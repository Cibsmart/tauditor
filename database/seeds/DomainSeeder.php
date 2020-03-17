<?php

use App\Domain;
use Illuminate\Database\Seeder;

class DomainSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Domain::class)->create([
          'slug' => 'STATE',
          'name' => 'ANAMBRA STATE GOVERNMENT',
        ]);

        factory(Domain::class)->create([
          'slug' => 'JAAC',
          'name' => 'ANAMBRA STATE JOINT ALLOCATION ACCOUNT COMMITTEE',
        ]);
    }
}
