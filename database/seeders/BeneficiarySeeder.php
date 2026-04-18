<?php

namespace Database\Seeders;


use App\Models\Bank;
use App\Models\BankDetail;
use App\Models\Beneficiary;
use App\Models\Domain;
use App\Models\Gender;
use App\Models\LocalGovernment;
use App\Models\MaritalStatus;
use App\Models\MicroFinanceBank;
use App\Models\State;
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

        foreach ($domains as $domain) {
            $beneficiaries = factory(Beneficiary::class, 50)->create([
                'domain_id'           => fn () => $domain->id,
                'gender_id'           => fn () => $gender->random()->id,
                'marital_status_id'   => fn () => $marital->random()->id,
                'state_id'            => fn () => $state->random()->id,
                'local_government_id' => fn () => $lga->random()->id,
                'beneficiary_type_id' => fn () => $domain->beneficiaryTypes->random()->id,
            ]);

            $beneficiaries->each(function ($beneficiary) use ($banks, $mfbs, $faker) {
                $faker->randomElement([1, 2]) == 1
                    ? $banks->random()->beneficiaries()
                            ->save(factory(BankDetail::class)->make(['beneficiary_id' => $beneficiary->id]))
                    : $mfbs->random()->beneficiaries()
                           ->save(factory(BankDetail::class)->make(['beneficiary_id' => $beneficiary->id]));
            });
        }
    }
}
