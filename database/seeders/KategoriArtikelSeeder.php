<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KategoriArtikel;

class KategoriArtikelSeeder extends Seeder
{
    public function run(): void
    {
        KategoriArtikel::insert([
            ['kategori' => 'Berita', 'created_at' => now(), 'updated_at' => now()],
            ['kategori' => 'Teknologi', 'created_at' => now(), 'updated_at' => now()],
            ['kategori' => 'Informasi', 'created_at' => now(), 'updated_at' => now()],
            ['kategori' => 'Pengumuman', 'created_at' => now(), 'updated_at' => now()],
            ['kategori' => 'Kegiatan Mahasiswa', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
