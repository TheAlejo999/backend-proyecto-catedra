<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VehicleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
{
    \App\Models\Vehicle::factory()->count(20)->create([
        'fleet_id' => \App\Models\Fleet::inRandomOrder()->first()->id ?? null,
        'driver_id' => null
    ]);
}
}
