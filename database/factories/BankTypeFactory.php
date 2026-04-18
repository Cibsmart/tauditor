<?php

namespace Database\Factories;

use App\Models\BankType;
use Illuminate\Database\Eloquent\Factories\Factory;

class BankTypeFactory extends Factory
{
    protected $model = BankType::class;

    public function definition(): array
    {
        return [
            'id'   => $this->faker->countryCode,
            'name' => $this->faker->country,
        ];
    }
}
