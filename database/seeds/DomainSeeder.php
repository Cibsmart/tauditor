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
          'name' => 'Anambra State Government',
        ]);

        factory(Domain::class)->create([
          'slug' => 'JAAC',
          'name' => 'Anambra State Joint Allocation Account Committee',
        ]);
    }
}
