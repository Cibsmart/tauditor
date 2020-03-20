<?php

use App\Bank;
use App\Domain;
use App\Gender;
use App\NextOfKin;
use App\BankDetail;
use App\Beneficiary;
use App\Relationship;
use App\MaritalStatus;
use App\Qualification;
use App\MicroFinanceBank;
use App\QualificationType;
use Illuminate\Database\Seeder;
use Faker\Generator as Faker;

class BeneficiarySeeder extends Seeder
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
        $relationships = Relationship::all();
        $qualifications = QualificationType::all();

        $beneficiaries = factory(Beneficiary::class, 50)->create([
          'domain_id' => fn () => $domains->random()->id,
          'gender_id' => fn () => $gender->random()->id,
          'marital_status_id' => fn () => $marital->random()->id,
          'state_id' => 1,
          'local_government_id' => 1,
        ]);

        $beneficiaries->each(function ($beneficiary) use ($faker, $banks, $mfbs, $relationships, $qualifications){
            $faker->randomElement([1, 2]) == 1
                ? $banks->random()->beneficiaries()->save(factory(BankDetail::class)->make([ 'beneficiary_id' => $beneficiary->id ]))
                : $mfbs->random()->beneficiaries()->save(factory(BankDetail::class)->make([ 'beneficiary_id' => $beneficiary->id ]));

            $beneficiary->next_of_kin()
                        ->save(factory(NextOfKin::class)
                            ->make(['relationship_id' => $relationships->random()->id]));

            $beneficiary->qualifications()
                        ->saveMany(factory(Qualification::class, $faker->randomElement([1,2,3,4,5]))
                            ->make(['qualification_type_id' => $qualifications->random()->id]));
        });
    }
}
