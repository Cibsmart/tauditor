<?php

use App\Bank;
use App\Domain;
use App\Gender;
use App\Employee;
use App\EmployeeBank;
use App\MaritalStatus;
use App\MicroFinanceBank;
use Illuminate\Database\Seeder;
use Faker\Generator as Faker;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @param  Faker  $faker
     * @return void
     */
    public function run(Faker $faker)
    {
        $domains = Domain::all();
        $gender = Gender::all();
        $marital = MaritalStatus::all();
        $banks = Bank::all();
        $mfbs = MicroFinanceBank::all();

        $employees = factory(Employee::class, 50)->create([
          'domain_id' => fn () => $domains->random()->id,
          'gender_id' => fn () => $gender->random()->id,
          'marital_status_id' => fn () => $marital->random()->id,
          'state_id' => 1,
          'local_government_id' => 1,
        ]);

        $employees->each(
            fn($employee) => $faker->randomElement([1, 2]) == 1
                ? $banks->random()->employees()->save(factory(EmployeeBank::class)->make([ 'employee_id' => $employee->id ]))
                : $mfbs->random()->employees()->save(factory(EmployeeBank::class)->make([ 'employee_id' => $employee->id ]))
        );
    }
}
