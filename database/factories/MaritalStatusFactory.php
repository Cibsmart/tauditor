<?php

namespace Database\Factories;

use App\Models\MaritalStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class MaritalStatusFactory extends Factory
{
    protected $model = MaritalStatus::class;

    public function definition(): array
    {
        $status = $this->faker->randomElement(['S', 'M']);

        return [
            'id'   => $status,
            'name' => $status === 'S' ? 'Single' : 'Married',
        ];
    }
}
