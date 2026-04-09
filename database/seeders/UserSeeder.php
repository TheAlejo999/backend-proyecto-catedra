<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
{
    \App\Models\User::factory()
        ->count(20)
        ->create([
            'role_id' => \App\Models\Role::inRandomOrder()->first()->id ?? null
        ]);
}
}
