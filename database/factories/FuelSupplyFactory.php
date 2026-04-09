<?php

namespace Database\Factories;

use App\Models\FuelSupply;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FuelSupply>
 */
class FuelSupplyFactory extends Factory
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
        'amount_gallons' => 10,
        'price_per_gallon' => 4.6,
        'total_cost' => 46,
        'date' => now(),
        'status' => 'pendiente',
    ];
}
}
