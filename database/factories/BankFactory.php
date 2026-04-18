<?php

namespace Database\Factories;

use App\Models\Bank;
use Illuminate\Database\Eloquent\Factories\Factory;

class BankFactory extends Factory
{
    protected $model = Bank::class;

    public function definition(): array
    {
        return [
            'code' => $this->faker->randomNumber(3, true),
            'name' => $this->faker->company,
        ];
    }
}
