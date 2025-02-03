<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PertandinganVenueSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('pertandingan_venue')->insert([
            ['pertandingan_id' => 1, 'venue_id' => 1],
        ]);
    }
}
