<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class VehicleFactory extends Factory
{
    public function definition(): array
{
    return [
        'fleet_id' => null,
        'driver_id' => null,
        'plate_number' => fake()->unique()->bothify('P######'),
        'brand' => 'Toyota',
        'model' => 'Hilux',
        'year' => 2022,
        'type' => fake()->randomElement(['pickup', 'camion', 'sedan', 'rastra']),
        'capacity_weight_kg' => 5000,
        'current_mileage' => 10000,
        'fuel_percentage' => 80,
        'tank_capacity_gallons' => 80,
        'fuel_consumption_per_km' => 0.2,
        'status' => 'disponible',
    ];
}
}