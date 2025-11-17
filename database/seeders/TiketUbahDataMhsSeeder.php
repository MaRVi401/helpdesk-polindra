<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Layanan;
use App\Models\Tiket;
use App\Models\DetailTiketUbahDataMhs;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TiketUbahDataMhsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 1. Cari User Mahasiswa dengan ID 32
        $mahasiswaUser = User::where('id', 32)->where('role', 'mahasiswa')->first();
        
        // 2. Cari Layanan "Perubahan Data Mahasiswa"
        $layanan = Layanan::where('nama', 'Perubahan Data Mahasiswa')->first();

        if (!$mahasiswaUser || !$layanan) {
            $this->command->error('User ID 32 dengan role "mahasiswa" atau Layanan "Perubahan Data Mahasiswa" tidak ditemukan. Seeder ini akan dilewati.');
            return;
        }

        DB::beginTransaction();
        try {
            // 3. Buat detail
            $detail = DetailTiketUbahDataMhs::create([
                'data_nama_lengkap' => 'Budi Santoso Susilo',
                'data_tmp_lahir' => 'Indramayu',
                'data_tgl_lhr' => '2004-05-10',
            ]);

            // 4. Buat tiket
            $tiket = new Tiket([
                'pemohon_id' => $mahasiswaUser->id, // Langsung pakai ID 32
                'layanan_id' => $layanan->id,
                'deskripsi' => 'Nama saya di SIAKAD salah ketik, seharusnya "Budi Santoso Susilo". Mohon diperbaiki. Saya lampirkan KTP dan Ijazah.',
                'no_tiket' => 'TKT-' . time() . Str::random(4),
                'status' => 'diproses',
                'prioritas' => 'sedang',
                // 'lampiran' => 'lampiran_tiket/ktp_ijazah_contoh.pdf'
            ]);

            // 5. Asosiasikan
            $tiket->detail()->associate($detail);
            $tiket->save();

            DB::commit();
            $this->command->info('Tiket Ubah Data Mahasiswa (User ID 32) berhasil dibuat.');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Gagal membuat tiket: ' . $e->getMessage());
        }
    }
}