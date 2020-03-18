<?php

use App\Domain;
use App\Gender;
use App\Employee;
use App\MaritalStatus;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $domains = Domain::all();
        $gender = Gender::all();
        $marital = MaritalStatus::all();

        factory(Employee::class, 50)->create([
          'domain_id' => fn () => $domains->random()->id,
          'gender_id' => fn () => $gender->random()->id,
          'marital_status_id' => fn () => $marital->random()->id,
          'state_id' => 1,
          'local_government_id' => 1,
        ]);
    }
}
