<?php

use App\Bank;
use App\State;
use App\Domain;
use App\Gender;
use App\BasicPay;
use App\NextOfKin;
use App\MdaDetail;
use App\BankDetail;
use App\WorkDetail;
use App\Beneficiary;
use App\Relationship;
use App\SalaryDetail;
use App\MaritalStatus;
use App\Qualification;
use App\LocalGovernment;
use App\MicroFinanceBank;
use App\StructuredSalary;
use App\QualificationType;
use App\PersonalizedSalary;
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
        $banks = Bank::all();
        $state = State::all();
        $gender = Gender::all();
        $domains = Domain::all();
        $lga = LocalGovernment::all();
        $mfbs = MicroFinanceBank::all();
        $marital = MaritalStatus::all();
        $relationships = Relationship::all();
        $qualifications = QualificationType::all();

        foreach ($domains as $domain) {
            $beneficiaries = factory(Beneficiary::class, 50)->create([
                'domain_id' => fn() => $domain->id,
                'gender_id' => fn() => $gender->random()->id,
                'marital_status_id' => fn() => $marital->random()->id,
                'state_id' => fn() => $state->random()->id,
                'local_government_id' => fn() => $lga->random()->id,
                'beneficiary_type_id' => fn() => $domain->beneficiaryTypes->random()->id,
            ]);

            $beneficiaries->each(function ($beneficiary) use ($banks, $mfbs, $relationships, $qualifications, $domain, $faker){

                $faker->randomElement([1,2]) == 1
                    ? $banks->random()->beneficiaries()->save(factory(BankDetail::class)->make(['beneficiary_id' => $beneficiary->id]))
                    : $mfbs->random()->beneficiaries()->save(factory(BankDetail::class)->make(['beneficiary_id' => $beneficiary->id]));

                $payable = factory(PersonalizedSalary::class)->create();

                $payable->salary()->save(factory(SalaryDetail::class)->make(['beneficiary_id' => $beneficiary->id]));

                $beneficiary->nextOfKin()
                            ->save(factory(NextOfKin::class)
                                ->make(['relationship_id' => fn() => $relationships->random()->id]));

                $beneficiary->qualifications()
                            ->saveMany(factory(Qualification::class, $faker->randomElement([1, 2, 3, 4, 5]))
                                ->make(['qualification_type_id' => fn() => $qualifications->random()->id]));

                $beneficiary->mdaDetail()
                            ->save(factory(MdaDetail::class)
                                ->make($this->mda_attributes($beneficiary)));

                $beneficiary->workDetail()
                            ->save(factory(WorkDetail::class)
                                ->make([
                                    'beneficiary_id' => $beneficiary->id,
                                    'designation_id' => $beneficiary->beneficiaryType->designations->random()->id,
                                ]));
            });
        }
    }

    public function mda_attributes($beneficiary)
    {
        $beneficiary_type = $beneficiary->beneficiaryType;

        //Pick a random MDA and assign to beneficiary
        $mda = $beneficiary_type->mdas->random();
        $mda_attributes = ['beneficiary_id' => $beneficiary->id, 'mda_id' => $mda->id];

        //If Selected MDA has_sub then assign sub and sub_sub
        if($mda->has_sub) {
            $sub_mda = $mda->subs->random();
            $mda_attributes = $mda->has_sub
                ? array_merge($mda_attributes, ['sub_mda_id' => $sub_mda->id])
                : $mda_attributes;

            $mda_attributes = $sub_mda->has_sub
                ? array_merge($mda_attributes, ['sub_sub_mda_id' => $sub_mda->subs->random()->id])
                : $mda_attributes;
        }

        return $mda_attributes;
    }
}
