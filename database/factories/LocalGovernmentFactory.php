<?php

namespace Database\Factories;

use App\Models\LocalGovernment;
use Illuminate\Database\Eloquent\Factories\Factory;

class LocalGovernmentFactory extends Factory
{
    protected $model = LocalGovernment::class;

    public function definition(): array
    {
        return [
            'state_id' => 1,
            'name' => 'Aba',
        ];
    }
}
