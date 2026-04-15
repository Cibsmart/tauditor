<?php

use Database\Seeders\LoanStatusSeeder;
use Database\Seeders\NationalitySeeder;
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

            //            DomainSeeder::class,
            //            RolesAndPermissionsSeeder::class,
            //            PaymentTypeSeeder::class,
            //            BeneficiaryTypeSeeder::class,
            //            UserSeeder::class,
            GenderSeeder::class,
            MaritalStatusSeeder::class,
            NationalitySeeder::class,
            StateSeeder::class,
            LoanStatusSeeder::class,
            //            BankTypeSeeder::class,
            //            BankSeeder::class,
            //            MicroFinanceBankSeeder::class,
            //            PayCommSeeder::class,
            //            MdaSeeder::class,
            //            SubMdaSeeder::class,
            //            SubSubMdaSeeder::class,
            //            DesignationSeeder::class,
            //            StructureSeeder::class,
            //            AllowanceTypeSeeder::class,
            //            AllowanceNameSeeder::class,
            //            AllowanceSeeder::class,
            //            DeductionTypeSeeder::class,
            //            DeductionNameSeeder::class,
            //            DeductionSeeder::class,
            //            PaymentCredentialSeeder::class,
            //            BeneficiarySeeder::class,
        ]);
    }
}
