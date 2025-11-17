<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Layanan;
use App\Models\Tiket;
use App\Models\DetailTiketResetAkun;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TiketResetAkunSeeder extends Seeder
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
        
        // 2. Cari Layanan "Reset Akun"
        $layanan = Layanan::where('nama', 'Reset Akun')->first();

        if (!$mahasiswaUser || !$layanan) {
            $this->command->error('User ID 32 dengan role "mahasiswa" atau Layanan "Reset Akun" tidak ditemukan. Seeder ini akan dilewati.');
            return;
        }

        DB::beginTransaction();
        try {
            // 3. Buat detail
            $detail = DetailTiketResetAkun::create([
                'aplikasi' => 'sevima',
            ]);

            // 4. Buat tiket
            $tiket = new Tiket([
                'pemohon_id' => $mahasiswaUser->id, // Langsung pakai ID 32
                'layanan_id' => $layanan->id,
                'deskripsi' => 'Saya lupa password SIAKAD (Sevima). Mohon bantuan untuk reset password akun saya.',
                'no_tiket' => 'TKT-' . time() . Str::random(4),
                'status' => 'menunggu',
                'prioritas' => 'tinggi',
            ]);

            // 5. Asosiasikan
            $tiket->detail()->associate($detail);
            $tiket->save();

            DB::commit();
            $this->command->info('Tiket Reset Akun (User ID 32) berhasil dibuat.');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Gagal membuat tiket: ' . $e->getMessage());
        }
    }
}