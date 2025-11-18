<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Layanan;
use Illuminate\Support\Facades\DB;

class TiketUbahDataMhsSeeder extends Seeder
{
    public function run()
    {
        $mahasiswaUser = User::where('role', 'mahasiswa')->first();
        if (!$mahasiswaUser) return; 

        $layanan = Layanan::where('nama', 'Ubah Data Mahasiswa')->first();
        if (!$layanan) {
            $this->command->error('Layanan Ubah Data tidak ditemukan.');
            return;
        }

        DB::beginTransaction();
        try {
            $tiketId = DB::table('tiket')->insertGetId([
                'pemohon_id' => $mahasiswaUser->id,
                'layanan_id' => $layanan->id,
                'deskripsi' => 'Kesalahan data diri.',
                'no_tiket' => time() + 2,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('riwayat_status_tiket')->insert([
                'tiket_id' => $tiketId,
                'user_id' => $mahasiswaUser->id,
                'status' => 'Pending', 
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('detail_tiket_ubah_data_mhs')->insert([
                'tiket_id' => $tiketId,
                'data_nama_lengkap' => 'Nama Koreksi',
                'data_tmp_lahir' => 'Indramayu',
                'data_tgl_lhr' => '2004-01-01',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();
            $this->command->info('Tiket Ubah Data berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Gagal: ' . $e->getMessage());
        }
    }
}