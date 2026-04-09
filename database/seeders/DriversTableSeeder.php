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
                'user_id' => 2,
                'license_number' => 'A1234567',
                'license_expiration' => now()->addYear(),
                'is_available' => true,
            ],
        ]);
    }
}
