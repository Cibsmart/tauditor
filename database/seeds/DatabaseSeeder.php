<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            DomainSeeder::class,
            BeneficiaryTypeSeeder::class,
            UserSeeder::class,
            GenderSeeder::class,
            MaritalStatusSeeder::class,
            StateSeeder::class,
            BankSeeder::class,
            MicroFinanceBankSeeder::class,
            MdaSeeder::class,
            SubMdaSeeder::class,
            DesignationSeeder::class,
            BeneficiarySeeder::class,
        ]);
    }
}
