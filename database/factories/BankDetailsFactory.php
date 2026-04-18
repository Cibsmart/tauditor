<?php

namespace Database\Factories;

use App\Models\BankDetail;
use App\Models\Beneficiary;
use Illuminate\Database\Eloquent\Factories\Factory;

class BankDetailsFactory extends Factory
{
    protected $model = BankDetail::class;

    public function definition(): array
    {
        return [
            'account_number' => $this->faker->bankAccountNumber,
            'beneficiary_id' => Beneficiary::factory(),
        ];
    }
}
