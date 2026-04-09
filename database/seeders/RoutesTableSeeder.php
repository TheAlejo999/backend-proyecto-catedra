<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoutesTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('routes')->insert([
            [
                'origin' => 'San Salvador',
                'destination' => 'Santa Ana',
                'distance_km' => 60,
                'estimated_time' => '01:30:00',
            ],
        ]);
    }
}
