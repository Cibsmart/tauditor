<?php

namespace Database\Factories;

use App\Models\Mda;
use App\Models\SubMda;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubMdaFactory extends Factory
{
    protected $model = SubMda::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->country,
            'mda_id' => Mda::factory(),
        ];
    }
}
