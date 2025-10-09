<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Layanan;
use App\Models\Unit;
use Illuminate\Support\Facades\DB;

class LayananSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Mengosongkan tabel dengan aman
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Layanan::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 2. Mengambil data Unit yang dibuat di MasterDataSeeder
        $akademik = Unit::where('nama_unit', 'Akademik')->first();
        $upaTik = Unit::where('nama_unit', 'UPA TIK')->first();
        $kemahasiswaan = Unit::where('nama_unit', 'Kemahasiswaan')->first();

        // 3. Validasi untuk memastikan Unit ada sebelum digunakan
        if (!$akademik || !$upaTik || !$kemahasiswaan) {
            $this->command->error('Satu atau lebih unit (Akademik/UPA TIK/Kemahasiswaan) tidak ditemukan. Pastikan MasterDataSeeder sudah dijalankan terlebih dahulu.');
            return;
        }

        // 4. Data layanan dummy sesuai skema tabel dan unit yang benar
        $layanans = [
            [
                'unit_id' => $akademik->id,
                'nama' => 'Surat Keterangan Aktif Kuliah',
                'status_arsip' => true, // boolean (1)
                'prioritas' => 2,        // integer (1=Rendah, 2=Normal, 3=Tinggi)
            ],
            [
                'unit_id' => $upaTik->id,
                'nama' => 'Reset Akun E-Learning & Siakad',
                'status_arsip' => true,
                'prioritas' => 3,
            ],
            [
                'unit_id' => $akademik->id,
                'nama' => 'Ubah Data Mahasiswa',
                'status_arsip' => true,
                'prioritas' => 2,
            ],
            [
                'unit_id' => $kemahasiswaan->id,
                'nama' => 'Request Publikasi',
                'status_arsip' => true,
                'prioritas' => 1,
            ]
        ];

        // 5. Masukkan data ke database
        foreach ($layanans as $layanan) {
            Layanan::create($layanan);
        }

        $this->command->info('LayananSeeder berhasil dijalankan dengan unit dari MasterDataSeeder.');
    }
}

