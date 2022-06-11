<?php

use App\Models\Step;
use Illuminate\Database\Seeder;

class StepSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 1; $i <= 15; $i++) {
            $code = str_pad($i, 2, '0', STR_PAD_LEFT);

            factory(Step::class)->create(['code' => $code, 'name' => 'Step ' . $code]);
        }
    }
}
