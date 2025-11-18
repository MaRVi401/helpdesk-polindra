<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Layanan;
use App\Models\Tiket;
use Illuminate\Support\Facades\DB;

class TiketSuratAktifSeeder extends Seeder
{
    public function run()
    {
        $mahasiswaUser = User::where('role', 'mahasiswa')->first();

        
        if (!$mahasiswaUser) {
            $this->command->warn('User mahasiswa tidak ditemukan. Membuat user dummy baru...');
            $mahasiswaUser = User::create([
                'name' => 'Mahasiswa Dummy',
                'email' => 'mahasiswa_dummy@polindra.ac.id',
                'password' => bcrypt('password'),
                'role' => 'mahasiswa'
            ]);
        }

        $layanan = Layanan::where('nama', 'Surat Keterangan Aktif Kuliah')->first();

        if (!$layanan) {
            $this->command->error('Layanan "Surat Keterangan Aktif Kuliah" tidak ditemukan. Pastikan LayananHelpdeskSeeder sudah dijalankan.');
            return;
        }

        DB::beginTransaction();
        try {
            $tiketId = DB::table('tiket')->insertGetId([
                'pemohon_id' => $mahasiswaUser->id, 
                'layanan_id' => $layanan->id,
                'deskripsi' => 'Mohon dibuatkan surat keterangan aktif untuk keperluan pendaftaran beasiswa KIP.',
                'no_tiket' => time(),
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

            DB::table('detail_tiket_surat_ket_aktif')->insert([
                'tiket_id' => $tiketId,
                'keperluan' => 'Pendaftaran Beasiswa KIP',
                'tahun_ajaran' => '2024',
                'semester' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();
            $this->command->info('Tiket Surat Aktif berhasil dibuat untuk User ID: ' . $mahasiswaUser->id);
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Gagal: ' . $e->getMessage());
        }
    }
}