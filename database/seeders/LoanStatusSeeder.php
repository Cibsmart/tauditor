<?php

namespace Database\Seeders;

use App\Models\LoanStatus;
use Illuminate\Database\Seeder;

class LoanStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        LoanStatus::create(['id' => 'A', 'name' => 'ACTIVE']);
        LoanStatus::create(['id' => 'C', 'name' => 'CANCELLED']);
        LoanStatus::create(['id' => 'P', 'name' => 'PAID']);
    }
}
