<?php

namespace Database\Factories;

use App\Models\Beneficiary;
use App\Models\BeneficiaryType;
use App\Models\Domain;
use App\Models\Gender;
use App\Models\LocalGovernment;
use App\Models\MaritalStatus;
use App\Models\State;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class BeneficiaryFactory extends Factory
{
    protected $model = Beneficiary::class;

    public function definition(): array
    {
        return [
            'last_name' => $this->faker->lastName,
            'first_name' => $this->faker->firstName,
            'middle_name' => $this->faker->firstName,
            'date_of_birth' => Carbon::now()->subYears($this->faker->numberBetween(20, 60)),
            'gender_id' => Gender::factory(),
            'marital_status_id' => MaritalStatus::factory(),
            'state_id' => State::factory(),
            'local_government_id' => LocalGovernment::factory(),
            'phone_number' => $this->faker->phoneNumber,
            'email' => $this->faker->email,
            'address_line_1' => $this->faker->streetAddress,
            'address_line_2' => $this->faker->secondaryAddress,
            'address_city' => $this->faker->city,
            'address_state' => $this->faker->state,
            'address_country' => $this->faker->country,
            'pensioner' => 0,
            'domain_id' => Domain::factory(),
            'beneficiary_type_id' => BeneficiaryType::factory(),
            'nationality_id' => 'NG',
        ];
    }

    public function pensioner(): static
    {
        return $this->state(['pensioner' => 1]);
    }
}
