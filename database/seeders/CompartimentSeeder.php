<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompartimentSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        DB::table('compartiments')->insert([
            ['nom' => 'VLounge-Sportbar', 'created_at' => $now, 'updated_at' => $now],
            ['nom' => 'VCoworking',        'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}
