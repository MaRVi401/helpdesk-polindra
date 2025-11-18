<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Layanan;
use Illuminate\Support\Facades\DB;

class TiketReqPublikasiSeeder extends Seeder
{
    public function run()
    {
        $mahasiswaUser = User::where('role', 'mahasiswa')->first();
        if (!$mahasiswaUser) return;

        $layanan = Layanan::where('nama', 'Request Publikasi Event')->first();
        if (!$layanan) {
            $this->command->error('Layanan Publikasi tidak ditemukan.');
            return;
        }

        DB::beginTransaction();
        try {
            $tiketId = DB::table('tiket')->insertGetId([
                'pemohon_id' => $mahasiswaUser->id,
                'layanan_id' => $layanan->id,
                'deskripsi' => 'Publikasi Event BEM.',
                'no_tiket' => time() + 3,
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

            DB::table('detail_tiket_req_publikasi')->insert([
                'tiket_id' => $tiketId,
                'judul' => 'Event Lomba',
                'kategori' => 'Akademik',
                'konten' => 'Deskripsi konten lomba.',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();
            $this->command->info('Tiket Publikasi berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Gagal: ' . $e->getMessage());
        }
    }
}