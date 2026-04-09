<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VehicleRouteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
{
    for ($i = 0; $i < 20; $i++) {
        \App\Models\VehicleRoute::create([
            'vehicle_id' => \App\Models\Vehicle::inRandomOrder()->first()->id,
            'route_id' => \App\Models\Route::inRandomOrder()->first()->id,
            'load_weight' => rand(50, 500),
            'estimated_fuel' => rand(5, 20),
            'departure_datetime' => now(),
            'estimated_arrival_datetime' => now()->addHour(),
            'status' => 'pendiente'
        ]);
    }
}
}
