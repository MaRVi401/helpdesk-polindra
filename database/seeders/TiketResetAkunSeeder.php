<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Layanan;
use Illuminate\Support\Facades\DB;

class TiketResetAkunSeeder extends Seeder
{
    public function run()
    {
        $mahasiswaUser = User::where('role', 'mahasiswa')->first();
        if (!$mahasiswaUser) {
            $this->command->error('User mahasiswa tidak ditemukan. Jalankan TiketSuratAktifSeeder terlebih dahulu untuk membuat user dummy.');
            return;
        }

        $layanan = Layanan::where('nama', 'Reset Akun E-Learning & Siakad')->first();
        if (!$layanan) {
            $this->command->error('Layanan Reset Akun tidak ditemukan.');
            return;
        }

        DB::beginTransaction();
        try {
            $tiketId = DB::table('tiket')->insertGetId([
                'pemohon_id' => $mahasiswaUser->id,
                'layanan_id' => $layanan->id,
                'deskripsi' => 'Saya lupa password SIAKAD.',
                'no_tiket' => time() + 1,
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

            DB::table('detail_tiket_reset_akun')->insert([
                'tiket_id' => $tiketId,
                'aplikasi' => 'sevima',
                'deskripsi' => 'Tidak bisa login.',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();
            $this->command->info('Tiket Reset Akun berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Gagal: ' . $e->getMessage());
        }
    }
}