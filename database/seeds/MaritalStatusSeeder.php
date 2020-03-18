<?php

use App\MaritalStatus;
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
          'S' => 'Single',
          'M' => 'Married',
          'D' => 'Divorced',
          'W' => 'Widow(er)',
        ];

        foreach ($status as $key => $value) {
            factory(MaritalStatus::class)->create([
              'code' => $key,
              'name' => $value,
            ]);
        }
    }
}
