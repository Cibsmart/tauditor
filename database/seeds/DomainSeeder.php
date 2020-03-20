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
          'code' => 'STATE',
          'name' => 'Anambra State Government',
        ]);

        factory(Domain::class)->create([
          'code' => 'JAAC',
          'name' => 'Anambra State Joint Allocation Account Committee',
        ]);
    }
}
