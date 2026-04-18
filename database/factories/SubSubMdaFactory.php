<?php

namespace Database\Factories;

use App\Models\SubMda;
use App\Models\SubSubMda;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubSubMdaFactory extends Factory
{
    protected $model = SubSubMda::class;

    public function definition(): array
    {
        return [
            'name'       => $this->faker->country,
            'sub_mda_id' => SubMda::factory(),
        ];
    }
}
