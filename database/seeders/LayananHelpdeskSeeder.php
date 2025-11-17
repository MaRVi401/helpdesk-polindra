<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Layanan;
use App\Models\Unit; // Pastikan model Unit di-import
use Illuminate\Support\Facades\DB;

class LayananHelpdeskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 1. Dapatkan atau Buat Unit yang diperlukan oleh kode
        // Kita gunakan firstOrCreate untuk membuat unit ini JIKA belum ada.
        // Unit ini diperlukan agar dropdown di create.blade.php bisa menampilkan nama unit.
        $baak = Unit::firstOrCreate(['nama_unit' => 'Biro Administrasi Akademik dan Kemahasiswaan']);
        $ti = Unit::firstOrCreate(['nama_unit' => 'Teknologi Informasi']);
        $humas = Unit::firstOrCreate(['nama_unit' => 'Humas dan Publikasi']);

        // 2. Data Layanan yang sudah disesuaikan dengan skema Anda
        // - Menghapus deskripsi, estimasi_waktu, berkas_pendukung_required
        // - Mengganti prioritas 'sedang' -> 2, 'tinggi' -> 3, 'rendah' -> 1
        $layanans = [
            [
                'nama' => 'Surat Keterangan Aktif', // Nama ini yang dicari JS & Controller
                'unit_id' => $baak->id,
                'prioritas' => 2, // 2 = Sedang
                'status_arsip' => false,
            ],
            [
                'nama' => 'Reset Akun', // Nama ini yang dicari JS & Controller
                'unit_id' => $ti->id,
                'prioritas' => 3, // 3 = Tinggi
                'status_arsip' => false,
            ],
            [
                'nama' => 'Perubahan Data Mahasiswa', // Nama ini yang dicari JS & Controller
                'unit_id' => $baak->id,
                'prioritas' => 2, // 2 = Sedang
                'status_arsip' => false,
            ],
            [
                'nama' => 'Request Publikasi', // Nama ini yang dicari JS & Controller
                'unit_id' => $humas->id,
                'prioritas' => 1, // 1 = Rendah
                'status_arsip' => false,
            ],
        ];

        // 3. Masukkan ke database
        foreach ($layanans as $layanan) {
            // Gunakan firstOrCreate untuk membuat layanan HANYA JIKA nama tersebut belum ada
            Layanan::firstOrCreate(['nama' => $layanan['nama']], $layanan);
        }

        $this->command->info('LayananHelpdeskSeeder (fixed) berhasil dijalankan.');
    }
}