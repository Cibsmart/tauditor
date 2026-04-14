<?php

use App\Models\Bank;
use App\Models\BankDetail;
use App\Models\Beneficiary;
use App\Models\BeneficiaryStatus;
use App\Models\Domain;
use App\Models\Gender;
use App\Models\LocalGovernment;
use App\Models\MaritalStatus;
use App\Models\MdaDetail;
use App\Models\MicroFinanceBank;
use App\Models\NextOfKin;
use App\Models\PersonalizedSalary;
use App\Models\Qualification;
use App\Models\QualificationType;
use App\Models\Relationship;
use App\Models\SalaryDetail;
use App\Models\State;
use App\Models\WorkDetail;
use Faker\Generator as Faker;
use Illuminate\Database\Seeder;

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
                'domain_id'           => fn () => $domain->id,
                'gender_id'           => fn () => $gender->random()->id,
                'marital_status_id'   => fn () => $marital->random()->id,
                'state_id'            => fn () => $state->random()->id,
                'local_government_id' => fn () => $lga->random()->id,
                'beneficiary_type_id' => fn () => $domain->beneficiaryTypes->random()->id,
            ]);

            $beneficiaries->each(function ($beneficiary) use (
                $banks,
                $mfbs,
                $relationships,
                $qualifications,
                $domain,
                $faker
            ) {
                $beneficiary->status()->save(factory(BeneficiaryStatus::class)->make());

                $faker->randomElement([1, 2]) == 1
                    ? $banks->random()->beneficiaries()
                            ->save(factory(BankDetail::class)->make(['beneficiary_id' => $beneficiary->id]))
                    : $mfbs->random()->beneficiaries()
                           ->save(factory(BankDetail::class)->make(['beneficiary_id' => $beneficiary->id]));

                $payable = factory(PersonalizedSalary::class)->create();

                $payable->salary()->save(factory(SalaryDetail::class)->make(['beneficiary_id' => $beneficiary->id]));

                $beneficiary->nextOfKin()
                            ->save(factory(NextOfKin::class)
                                ->make(['relationship_id' => fn () => $relationships->random()->id]));

                $beneficiary->qualifications()
                            ->saveMany(factory(Qualification::class, $faker->randomElement([1, 2, 3, 4, 5]))
                                ->make(['qualification_type_id' => fn () => $qualifications->random()->id]));

                $beneficiary->mdaDetail()
                            ->save(factory(MdaDetail::class)
                                ->make($this->mdaAttributes($beneficiary)));

                $beneficiary->workDetail()
                            ->save(factory(WorkDetail::class)
                                ->make([
                                    'beneficiary_id' => $beneficiary->id,
                                    'designation_id' => $beneficiary->beneficiaryType->designations->random()->id,
                                    'grade_level_id' => 1,
                                    'step_id'        => 1,
                                ]));
            });
        }
    }

    public function mdaAttributes($beneficiary)
    {
        $beneficiary_type = $beneficiary->beneficiaryType;

        //Pick a random MDA and assign to beneficiary
        $mda = $beneficiary_type->mdas->random();
        $mda_attributes = ['beneficiary_id' => $beneficiary->id, 'mda_id' => $mda->id];

        //If Selected MDA has_sub then assign sub and sub_sub
        if ($mda->has_sub) {
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
