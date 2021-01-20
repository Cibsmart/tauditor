<?php

use Illuminate\Database\Seeder;
use Database\Seeders\NationalitySeeder;

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
            RolesAndPermissionsSeeder::class,
            PaymentTypeSeeder::class,
            BeneficiaryTypeSeeder::class,
            UserSeeder::class,
            GenderSeeder::class,
            MaritalStatusSeeder::class,
            RelationshipSeeder::class,
            NationalitySeeder::class,
            StateSeeder::class,
            BankTypeSeeder::class,
            BankSeeder::class,
            MicroFinanceBankSeeder::class,
            PayCommSeeder::class,
            MdaSeeder::class,
            SubMdaSeeder::class,
            SubSubMdaSeeder::class,
            DesignationSeeder::class,
//            QualificationTypeSeeder::class,
//            GradeLevelSeeder::class,
//            StepSeeder::class,
//            StructureSeeder::class,
//            ValueTypeSeeder::class,
//            SalaryTypeSeeder::class,
//            AssignableTypeSeeder::class,
//            AllowanceTypeSeeder::class,
//            AllowanceNameSeeder::class,
//            AllowanceSeeder::class,
//            DeductionTypeSeeder::class,
//            DeductionNameSeeder::class,
//            DeductionSeeder::class,
//            StatusSeeder::class,
            PaymentCredentialSeeder::class,
//            BeneficiarySeeder::class,
        ]);
    }
}
