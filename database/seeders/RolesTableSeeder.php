<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RolesTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('roles')->insert([
            ['name' => 'Administrador'],
            ['name' => 'Logística'],
            ['name' => 'Conductor'],
        ]);
    }
}
