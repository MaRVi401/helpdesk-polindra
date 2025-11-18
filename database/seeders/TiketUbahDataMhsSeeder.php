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
        $mahasiswaUser = User::where('id', 32)->where('role', 'mahasiswa')->first();
        $layanan = Layanan::where('nama', 'Ubah Data Mahasiswa')->first();

        if (!$mahasiswaUser || !$layanan) {
            $this->command->error('User ID 32 atau Layanan Ubah Data Mahasiswa tidak ditemukan.');
            return;
        }

        DB::beginTransaction();
        try {

            $tiketId = DB::table('tiket')->insertGetId([
                'pemohon_id' => $mahasiswaUser->id,
                'layanan_id' => $layanan->id,
                'deskripsi' => 'Terdapat kesalahan penulisan nama dan tanggal lahir pada data akademik saya.',
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
                'data_nama_lengkap' => 'Muhamad Hadid Faletehan',
                'data_tmp_lahir' => 'Indramayu',
                'data_tgl_lhr' => '2004-05-10', 
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();
            $this->command->info('Tiket Ubah Data Mahasiswa berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Gagal: ' . $e->getMessage());
        }
    }
}