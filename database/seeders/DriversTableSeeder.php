<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DriversTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('drivers')->insert([
            [
                'user_id' => 2, // Debe existir en users
                'license_number' => 'A1234567',
                'license_expiration' => now()->addYear()->toDateString(),
                'is_available' => true,
            ],
        ]);
    }
}
