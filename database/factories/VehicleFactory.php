<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class VehicleFactory extends Factory
{
    public function definition(): array
    {
        return [
            'plate_number' => fake()->unique()->bothify('P######'),
            'brand' => 'Toyota',
            'model' => 'Hilux',
            'year' => 2022,
            'type' => 'pickup',
            'capacity_weight_kg' => 1000,
            'current_mileage' => 10000,
            'fuel_percentage' => 80,
            'tank_capacity_gallons' => 20,
            'fuel_consumption_per_km' => 0.2,
            'status' => 'disponible',
        ];
    }
}