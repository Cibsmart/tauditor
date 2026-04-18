<?php

namespace Database\Factories;

use App\Models\Domain;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'first_name'        => $this->faker->name,
            'last_name'         => $this->faker->name,
            'email'             => $this->faker->unique()->safeEmail,
            'email_verified_at' => now(),
            'password'          => 'password',
            'domain_id'         => Domain::factory(),
            'remember_token'    => Str::random(10),
        ];
    }
}
