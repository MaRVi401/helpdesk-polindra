<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KategoriArtikel;

class KategoriArtikelSeeder extends Seeder
{
    public function run(): void
    {
        KategoriArtikel::insert([
            ['kategori' => 'Teknologi', 'created_at' => now(), 'updated_at' => now()],
            ['kategori' => 'Pendidikan', 'created_at' => now(), 'updated_at' => now()],
            ['kategori' => 'Kampus', 'created_at' => now(), 'updated_at' => now()],
            ['kategori' => 'Kegiatan Mahasiswa', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
