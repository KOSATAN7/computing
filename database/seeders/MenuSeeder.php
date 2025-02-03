<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        Menu::insert([
            [
                'venue_id' => 1,
                'nama' => 'Nasi Goreng GBK',
                'deskripsi' => 'Nasi goreng khas GBK.',
                'harga' => 25000,
                'foto' => 'nasi-goreng.png',
                'kategori' => 'Makanan',
                'kesediaan' => true,
            ],
            [
                'venue_id' => 2,
                'nama' => 'Es Teh Manis',
                'deskripsi' => 'Minuman favorit di Britama Arena.',
                'harga' => 10000,
                'foto' => 'es-teh.png',
                'kategori' => 'Minuman',
                'kesediaan' => true,
            ],
        ]);
    }
}
