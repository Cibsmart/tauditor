<?php

namespace Database\Factories;

use App\Models\Bank;
use App\Models\BeneficiaryType;
use App\Models\Domain;
use App\Models\PaymentCredential;
use App\Models\PaymentType;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentCredentialFactory extends Factory
{
    protected $model = PaymentCredential::class;

    public function definition(): array
    {
        return [
            'corporate_code' => $this->faker->randomElement(['TPA', 'TLA']),
            'payment_type_id' => PaymentType::factory(),
            'terminal_id' => $this->faker->randomNumber(8, true),
            'account_number' => $this->faker->bankAccountNumber,
            'account_name' => $this->faker->company,
            'pan' => $this->faker->bankAccountNumber,
            'account_type' => $this->faker->randomElement(['00', '10', '20']),
            'bank_id' => Bank::factory(),
            'beneficiary_type_id' => BeneficiaryType::factory(),
            'domain_id' => Domain::factory(),
        ];
    }
}
