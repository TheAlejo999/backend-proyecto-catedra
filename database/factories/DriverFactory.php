<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DriverFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'license_number' => fake()->unique()->bothify('A#######'),
            'license_expiration' => now()->addYears(2),
            'is_available' => true,
        ];
    }
}