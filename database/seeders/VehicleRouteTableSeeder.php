<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VehicleRouteTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('vehicle_routes')->insert([
            [
                'vehicle_id' => 1,
                'route_id' => 1,
                'load_weight' => 1000,
                'estimated_fuel' => 15,
                'departure_datetime' => now(),
                'estimated_arrival_datetime' => now()->addHours(2),
                'status' => 'en_progreso',
            ],
        ]);
    }
}
