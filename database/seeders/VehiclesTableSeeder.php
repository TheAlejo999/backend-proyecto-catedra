<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VehiclesTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('vehicles')->insert([
            [
                'fleet_id' => 1,
                'driver_id' => 1,
                'plate_number' => 'P123ABC',
                'brand' => 'Toyota',
                'model' => 'Hilux',
                'year' => 2022,
                'tank_capacity_gallons' => 80,
                'type' => 'camion',       
                'capacity_weight_kg' => 5000,
                'current_mileage' => 10000,
                'fuel_percentage' => 80,
                'fuel_consumption_per_km' => 0.2,
                'status' => 'disponible', 
            ],
        ]);
    }
}
