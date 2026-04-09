<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FleetsTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('fleets')->insert([
            [
                'name' => 'Flota Liviana',
                'type' => 'liviana',
                'description' => 'Flota de vehículos livianos',
            ],
            [
                'name' => 'Flota Pesada',
                'type' => 'pesada',
                'description' => 'Flota de vehículos pesados',
            ],
        ]);
    }
}
