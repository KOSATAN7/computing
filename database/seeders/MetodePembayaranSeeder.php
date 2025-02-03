<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MetodePembayaran;

class MetodePembayaranSeeder extends Seeder
{
    public function run(): void
    {
        MetodePembayaran::insert([
            ['nama' => 'Transfer Bank', 'deskripsi' => 'Pembayaran melalui transfer bank.', 'aktif' => true],
            ['nama' => 'E-Wallet', 'deskripsi' => 'Pembayaran melalui e-wallet seperti Gopay, OVO.', 'aktif' => true],
            ['nama' => 'Kartu Kredit', 'deskripsi' => 'Pembayaran menggunakan kartu kredit.', 'aktif' => true],
        ]);
    }
}
