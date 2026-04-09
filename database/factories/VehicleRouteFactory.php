<?php

namespace Database\Factories;

use App\Models\VehicleRoute;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<VehicleRoute>
 */
class VehicleRouteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
{
    return [
        'vehicle_id' => \App\Models\Vehicle::factory(),
        'route_id' => \App\Models\Route::factory(),
        'load_weight' => 100,
        'estimated_fuel' => 10,
        'departure_datetime' => now(),
        'estimated_arrival_datetime' => now()->addHour(),
        'status' => 'pendiente'
    ];
}
}
