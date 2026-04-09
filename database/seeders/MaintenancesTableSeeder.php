<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MaintenancesTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('maintenances')->insert([
            [
                'vehicle_id' => 1,
                'description' => 'Cambio de aceite',
                'cost' => 50.00,
                'date' => now(),
                'next_maintenance_mileage' => 15000,
            ],
        ]);
    }
}
