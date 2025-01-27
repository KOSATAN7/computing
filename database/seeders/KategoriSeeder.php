<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kategori;
use App\Models\SubKategori;

class KategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $kategoris = [
            [
                'nama' => 'Olahraga',
                'deskripsi' => 'Kategori untuk semua jenis olahraga',
                'subkategori' => [
                    ['nama' => 'Sepak Bola'],
                    ['nama' => 'Bisbol'],
                    ['nama' => 'Bola Basket'],
                    ['nama' => 'Formula 1'],
                    ['nama' => 'Bola Tangan'],
                    ['nama' => 'Hoki'],
                    ['nama' => 'MMA'],
                    ['nama' => 'Silat'],
                    ['nama' => 'Rugby'],
                    ['nama' => 'Bola Voli'],
                ],
            ],
            [
                'nama' => 'Film',
                'deskripsi' => 'Kategori untuk film dan hiburan',
                'subkategori' => [
                    ['nama' => 'Action'],
                    ['nama' => 'Comedy'],
                    ['nama' => 'Drama'],
                ],
            ],
            [
                'nama' => 'Musik',
                'deskripsi' => 'Kategori untuk acara musik dan konser',
                'subkategori' => [
                    ['nama' => 'Pop'],
                    ['nama' => 'Rock'],
                    ['nama' => 'Jazz'],
                ],
            ],
            [
                'nama' => 'Komedi',
                'deskripsi' => 'Kategori untuk acara stand-up comedy dan humor',
                'subkategori' => [
                    ['nama' => 'Stand-up Comedy'],
                    ['nama' => 'Improvisasi'],
                    ['nama' => 'Skit'],
                ],
            ],
        ];


        foreach ($kategoris as $kategoriData) {

            $kategori = Kategori::create([
                'nama' => $kategoriData['nama'],
                'deskripsi' => $kategoriData['deskripsi'],
            ]);


            foreach ($kategoriData['subkategori'] as $subkategoriData) {
                $kategori->subkategori()->create($subkategoriData);
            }
        }
    }
}
