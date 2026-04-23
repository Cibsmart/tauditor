<?php

namespace Database\Factories;

use App\Models\Bank;
use App\Models\Domain;
use App\Models\MicroFinanceBank;
use Illuminate\Database\Eloquent\Factories\Factory;

class MicrofinanceBankFactory extends Factory
{
    protected $model = MicroFinanceBank::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->company,
            'account_number' => $this->faker->bankAccountNumber,
            'bank_id' => Bank::factory(),
            'domain_id' => Domain::factory(),
        ];
    }
}
