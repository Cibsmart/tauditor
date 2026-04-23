<?php

namespace Database\Factories;

use App\Models\Domain;
use App\Models\PayComm;
use Illuminate\Database\Eloquent\Factories\Factory;

class PayCommFactory extends Factory
{
    protected $model = PayComm::class;

    public function definition(): array
    {
        return [
            'code' => $this->faker->countryCode,
            'name' => $this->faker->country,
            'account_number' => $this->faker->bankAccountNumber,
            'commission' => $this->faker->numberBetween(50, 200),
            'bankable_type' => $this->faker->randomElement(['commercial', 'micro_finance']),
            'bankable_id' => $this->faker->numberBetween(1, 15),
            'domain_id' => Domain::factory(),
        ];
    }
}
