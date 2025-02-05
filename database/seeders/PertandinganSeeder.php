<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pertandingan;

class PertandinganSeeder extends Seeder
{
    public function run(): void
    {
        Pertandingan::insert([
            [
                'cabang_olahraga' => 'Sepak Bola',
                'liga' => 'Liga 1 IDN',
                'tim_tuan_rumah' => 'Persija Jakarta',
                'logo_tuan_rumah' => 'persija.png',
                'tim_tamu' => 'Persebaya Surabaya',
                'logo_tamu' => 'persebaya.png',
                'tanggal_pertandingan' => '2025-06-15',
                'waktu_pertandingan' => '19:30:00',
                'status_pertandingan' => 'upcoming',
                'status' => 'aktif',
            ],
            [
                'cabang_olahraga' => 'Basket',
                'liga' => 'IBL',
                'tim_tuan_rumah' => 'Satria Muda',
                'logo_tuan_rumah' => 'satriamuda.png',
                'tim_tamu' => 'Pelita Jaya',
                'logo_tamu' => 'pelitajaya.png',
                'tanggal_pertandingan' => '2025-06-20',
                'waktu_pertandingan' => '18:00:00',
                'status_pertandingan' => 'ongoing',
                'status' => 'aktif',
            ],
        ]);
    }
}
