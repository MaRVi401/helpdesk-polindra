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
        $mahasiswaUser = User::where('id', 32)->where('role', 'mahasiswa')->first();
        $layanan = Layanan::where('nama', 'Request Publikasi Event')->first();

        if (!$mahasiswaUser || !$layanan) {
            $this->command->error('User ID 32 atau Layanan Request Publikasi Event tidak ditemukan.');
            return;
        }

        DB::beginTransaction();
        try {

            $tiketId = DB::table('tiket')->insertGetId([
                'pemohon_id' => $mahasiswaUser->id,
                'layanan_id' => $layanan->id,
                'deskripsi' => 'Kami dari BEM ingin mengajukan permohonan publikasi poster kegiatan LDK.',
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
                'judul' => 'Open Recruitment LDK 2025',
                'kategori' => 'Kegiatan Mahasiswa',
                'konten' => 'Pendaftaran dibuka mulai tanggal 20 November sampai 30 November 2025 bertempat di sekretariat BEM.',
                'gambar' => null, // Opsional
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();
            $this->command->info('Tiket Request Publikasi berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Gagal: ' . $e->getMessage());
        }
    }
}