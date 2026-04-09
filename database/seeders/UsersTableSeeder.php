<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('users')->insert([
            [
                'role_id' => 1,
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'dui' => '00000000-0',
                'hiring_date' => now(),
            ],
            [
                'role_id' => 2,
                'name' => 'Logística User',
                'email' => 'logistica@example.com',
                'password' => Hash::make('password'),
                'dui' => '11111111-2',
                'hiring_date' => now(),
            ],
            [
                'role_id' => 3,
                'name' => 'Conductor User',
                'email' => 'conductor@example.com',
                'password' => Hash::make('password'),
                'dui' => '22222222-3',
                'hiring_date' => now(),
            ],
        ]);
    }
}
