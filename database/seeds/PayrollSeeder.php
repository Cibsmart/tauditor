<?php

use App\User;
use App\Domain;
use App\Payroll;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PayrollSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $curr_month = Carbon::now()->month;

        for($i = 1; $i <= $curr_month; $i++)
        {
            factory(Payroll::class)->create([
                'month' => $i,
                'year' => 2020,
                'user_id' => 1,
                'domain_id' => 1,
            ]);
        }
    }
}
