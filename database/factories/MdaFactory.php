<?php

namespace Database\Factories;

use App\Models\BeneficiaryType;
use App\Models\Mda;
use Illuminate\Database\Eloquent\Factories\Factory;

class MdaFactory extends Factory
{
    protected $model = Mda::class;

    public function definition(): array
    {
        return [
            'code'                => $this->faker->countryCode,
            'name'                => $this->faker->country,
            'beneficiary_type_id' => BeneficiaryType::factory(),
        ];
    }
}
