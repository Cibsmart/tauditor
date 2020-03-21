<?php

use App\Mda;
use App\Bank;
use App\State;
use App\Domain;
use App\Gender;
use App\NextOfKin;
use App\MdaDetail;
use App\BankDetail;
use App\Beneficiary;
use App\Relationship;
use App\MaritalStatus;
use App\Qualification;
use App\BeneficiaryType;
use App\LocalGovernment;
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
                'beneficiary_type_id' => fn() => $domain->beneficiary_types->random()->id,
            ]);

            $beneficiaries->each(function ($beneficiary) use ($banks, $mfbs, $relationships, $qualifications, $faker){

                $faker->randomElement([1,2]) == 1
                    ? $banks->random()->beneficiaries()->save(factory(BankDetail::class)->make(['beneficiary_id' => $beneficiary->id]))
                    : $mfbs->random()->beneficiaries()->save(factory(BankDetail::class)->make(['beneficiary_id' => $beneficiary->id]));

                $beneficiary->next_of_kin()
                            ->save(factory(NextOfKin::class)
                                ->make(['relationship_id' => fn() => $relationships->random()->id]));

                $beneficiary->qualifications()
                            ->saveMany(factory(Qualification::class, $faker->randomElement([1, 2, 3, 4, 5]))
                                ->make(['qualification_type_id' => fn() => $qualifications->random()->id]));

                $beneficiary->mda_details()
                            ->save(factory(MdaDetail::class)
                                ->make($this->mda_attributes($beneficiary)));
            });
        }
    }

    public function mda_attributes($beneficiary)
    {
        $beneficiary_type = $beneficiary->beneficiary_type;

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
