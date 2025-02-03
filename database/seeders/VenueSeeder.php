<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Venue;

class VenueSeeder extends Seeder
{
    public function run(): void
    {
        Venue::insert([
            [
                'admin_id' => 2,
                'pertandingan_id' => 1,
                'provider_id' => null,
                'nama' => 'Gelora Bung Karno',
                'alamat' => 'Jakarta',
                'kapasitas' => 80000,
                'kota' => 'Jakarta',
                'latitude' => '-6.2191',
                'longitude' => '106.8029',
                'kontak' => '021123456',
                'status' => 'aktif',
            ],
            [
                'admin_id' => 3,
                'pertandingan_id' => 2,
                'provider_id' => null,
                'nama' => 'Britama Arena',
                'alamat' => 'Jakarta',
                'kapasitas' => 5000,
                'kota' => 'Jakarta',
                'latitude' => '-6.2001',
                'longitude' => '106.7842',
                'kontak' => '021765432',
                'status' => 'aktif',
            ],
        ]);
    }
}
