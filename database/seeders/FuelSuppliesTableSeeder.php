<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FuelSuppliesTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('fuel_supplies')->insert([
            [
                'vehicle_id' => 1,
                'route_id' => 1,
                'amount_gallons' => 20,
                'price_per_gallon' => 4.25,
                'total_cost' => 85.00,
                'state' => 'APROBADO',
                'date' => now(),
            ],
        ]);
    }
}
