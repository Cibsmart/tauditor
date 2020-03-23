<?php

use App\GradeLevel;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

class GradeLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 1; $i <= 17; $i++){
            $code = str_pad($i, 2, '0', STR_PAD_LEFT);

            factory(GradeLevel::class)->create(['code' => $code, 'name' => 'GL ' . $code]);
        }
    }
}
