<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FuelSupplySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
{
    for ($i = 0; $i < 20; $i++) {
        \App\Models\FuelSupply::create([
            'vehicle_id' => \App\Models\Vehicle::inRandomOrder()->first()->id,
            'route_id' => \App\Models\Route::inRandomOrder()->first()->id,
            'amount_gallons' => rand(5, 20),
            'price_per_gallon' => 4.6,
            'total_cost' => rand(20, 100),
            'date' => now(),
            'status' => 'pendiente'
        ]);
    }
}
}
