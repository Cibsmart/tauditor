<?php

namespace Database\Factories;

use App\Models\BeneficiaryType;
use App\Models\Domain;
use Illuminate\Database\Eloquent\Factories\Factory;

class BeneficiaryTypeFactory extends Factory
{
    protected $model = BeneficiaryType::class;

    public function definition(): array
    {
        return [
            'id'        => $this->faker->countryCode,
            'name'      => $this->faker->country,
            'domain_id' => Domain::factory(),
        ];
    }
}
