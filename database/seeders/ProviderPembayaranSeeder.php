<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProviderPembayaran;

class ProviderPembayaranSeeder extends Seeder
{
    public function run(): void
    {
        ProviderPembayaran::insert([
            [
                'metode_pembayaran_id' => 1,
                'venue_id' => 1,
                'nama' => 'BCA GBK',
                'no_rek' => '1234567890',
                'penerima' => 'Gelora Bung Karno',
                'deskripsi' => 'Rekening pembayaran tiket GBK',
                'foto' => 'bca.png',
                'aktif' => true,
            ],
            [
                'metode_pembayaran_id' => 2,
                'venue_id' => 2,
                'nama' => 'OVO Britama Arena',
                'no_rek' => '0987654321',
                'penerima' => 'Britama Arena',
                'deskripsi' => 'E-wallet untuk Britama Arena',
                'foto' => 'ovo.png',
                'aktif' => true,
            ],
        ]);
    }
}
