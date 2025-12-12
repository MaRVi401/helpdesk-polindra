<?php

namespace Database\Seeders;

use App\Models\Artikel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ArtikelSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Artikel::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $artikels = [

            // 1. Berita (kategori_id = 1)
            [
                'user_id' => 1,
                'kategori_id' => 1,
                'judul' => 'Kampus Meluncurkan Sistem Layanan Terpadu Terbaru',
                'slug' => Str::slug('Kampus Meluncurkan Sistem Layanan Terpadu Terbaru'),
                'deskripsi' => 'Sistem layanan terpadu resmi diluncurkan hari ini untuk meningkatkan efisiensi layanan administrasi mahasiswa dan civitas akademika.',
                'gambar' => null,
                'status' => 'Post',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],

            // 2. Teknologi (kategori_id = 2)
            [
                'user_id' => 1,
                'kategori_id' => 2,
                'judul' => 'Pemanfaatan AI dalam Pengelolaan Administrasi Kampus',
                'slug' => Str::slug('Pemanfaatan AI dalam Pengelolaan Administrasi Kampus'),
                'deskripsi' => 'Artikel ini membahas bagaimana teknologi kecerdasan buatan (AI) mendukung proses administrasi modern di lingkungan perguruan tinggi.',
                'gambar' => null,
                'status' => 'Post',
                'created_at' => Carbon::now()->subDays(1),
                'updated_at' => Carbon::now()->subDays(1),
            ],

            // 3. Informasi (kategori_id = 3)
            [
                'user_id' => 1,
                'kategori_id' => 3,
                'judul' => 'Panduan Layanan Akademik untuk Mahasiswa Baru',
                'slug' => Str::slug('Panduan Layanan Akademik untuk Mahasiswa Baru'),
                'deskripsi' => 'Informasi lengkap mengenai prosedur layanan akademik yang harus diketahui mahasiswa baru, mulai dari KRS hingga administrasi umum.',
                'gambar' => null,
                'status' => 'Post',
                'created_at' => Carbon::now()->subDays(2),
                'updated_at' => Carbon::now()->subDays(2),
            ],

            // 4. Pengumuman (kategori_id = 4)
            [
                'user_id' => 1,
                'kategori_id' => 4,
                'judul' => 'Jadwal Libur Akademik Semester Ini',
                'slug' => Str::slug('Jadwal Libur Akademik Semester Ini'),
                'deskripsi' => 'Pengumuman resmi mengenai jadwal libur akademik yang berlaku untuk seluruh program studi pada semester ini.',
                'gambar' => null,
                'status' => 'Post',
                'created_at' => Carbon::now()->subDays(3),
                'updated_at' => Carbon::now()->subDays(3),
            ],

            // 5. Kegiatan Mahasiswa (kategori_id = 5)
            [
                'user_id' => 1,
                'kategori_id' => 5,
                'judul' => 'Dokumentasi Kegiatan Mahasiswa dalam Acara Expo Kampus',
                'slug' => Str::slug('Dokumentasi Kegiatan Mahasiswa dalam Acara Expo Kampus'),
                'deskripsi' => 'Rangkaian dokumentasi dan informasi kegiatan mahasiswa yang berpartisipasi dalam acara Expo Kampus tahun ini.',
                'gambar' => null,
                'status' => 'Post',
                'created_at' => Carbon::now()->subDays(4),
                'updated_at' => Carbon::now()->subDays(4),
            ],

            // 6. Artikel Panduan User
            [
                'user_id' => 1,
                'kategori_id' => 6,
                'judul' => 'Panduan Lengkap Penggunaan Sistem Layanan Terpadu',
                'slug' => Str::slug('Panduan Lengkap Penggunaan Sistem Layanan Terpadu'),
                'deskripsi' => 'Artikel panduan ini membantu pengguna memahami cara menggunakan fitur-fitur utama dalam sistem layanan terpadu.',
                'gambar' => null,
                'status' => 'Post',
                'created_at' => Carbon::now()->subDays(5),
                'updated_at' => Carbon::now()->subDays(5),
            ],
        ];

        Artikel::insert($artikels);
    }
}