<?php

use App\Models\MaritalStatus;
use Illuminate\Database\Seeder;

class MaritalStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $status = [
          'S' => 'SINGLE',
          'M' => 'MARRIED',
          'D' => 'DIVORCED',
          'W' => 'WIDOW(ER)',
        ];

        foreach ($status as $key => $value) {
            factory(MaritalStatus::class)->create([
              'id' => $key,
              'name' => $value,
            ]);
        }
    }
}
