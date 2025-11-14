<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Layanan;
use App\Models\Unit;
use Illuminate\Support\Facades\DB;

class LayananSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Layanan::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $akademik = Unit::where('nama_unit', 'Akademik')->first();
        $upaTik = Unit::where('nama_unit', 'UPA TIK')->first();
        $kemahasiswaan = Unit::where('nama_unit', 'Kemahasiswaan')->first();

        if (!$akademik || !$upaTik || !$kemahasiswaan) {
            $this->command->error('Unit (Akademik/UPA TIK/Kemahasiswaan) tidak ditemukan. Pastikan MasterDataSeeder/UnitSeeder sudah dijalankan.');
            return;
        }

        $layanans = [
            [
                'unit_id' => $akademik->id,
                'nama' => 'Surat Keterangan Aktif Kuliah',
                'status_arsip' => false,
                'prioritas' => 2,
            ],
            [
                'unit_id' => $upaTik->id,
                'nama' => 'Reset Akun',
                'status_arsip' => false,
                'prioritas' => 3,
            ],
            [
                'unit_id' => $akademik->id,
                'nama' => 'Ubah Data Mahasiswa',
                'status_arsip' => false,
                'prioritas' => 2,
            ],
            [
                'unit_id' => $kemahasiswaan->id,
                'nama' => 'Request Publikasi',
                'status_arsip' => false,
                'prioritas' => 1,
            ]
        ];

        foreach ($layanans as $layanan) {
            Layanan::create($layanan);
        }

        // $this->command->info('LayananSeeder berhasil dijalankan.');
    }
}