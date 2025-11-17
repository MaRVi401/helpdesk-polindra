<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Layanan;
use App\Models\Tiket;
use App\Models\DetailTiketSuratKetAktif;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TiketSuratAktifSeeder extends Seeder
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

        // 2. Cari Layanan "Surat Keterangan Aktif"
        $layanan = Layanan::where('nama', 'Surat Keterangan Aktif')->first();

        // Jika user atau layanan tidak ditemukan, jangan jalankan seeder
        if (!$mahasiswaUser || !$layanan) {
            $this->command->error('User ID 32 dengan role "mahasiswa" atau Layanan "Surat Keterangan Aktif" tidak ditemukan. Seeder ini akan dilewati.');
            return;
        }

        DB::beginTransaction();
        try {
            // 3. Buat data detailnya terlebih dahulu
            $detail = DetailTiketSuratKetAktif::create([
                'keperluan' => 'Pendaftaran Beasiswa KIP',
                'tahun_ajaran' => '2024/2025',
                'semester' => 3
            ]);

            // 4. Buat data tiket utama
            $tiket = new Tiket([
                'pemohon_id' => $mahasiswaUser->id, // Langsung pakai ID 32
                'layanan_id' => $layanan->id,
                'deskripsi' => 'Mohon dibuatkan surat keterangan aktif untuk keperluan pendaftaran beasiswa KIP. Terima kasih.',
                'no_tiket' => 'TKT-' . time() . Str::random(4),
                'status' => 'menunggu',
                'prioritas' => 'sedang',
                // 'lampiran' => 'path/ke/file_contoh.pdf' 
            ]);

            // 5. Asosiasikan tiket dengan detail
            $tiket->detail()->associate($detail);
            $tiket->save();

            DB::commit();
            $this->command->info('Tiket Surat Keterangan Aktif (User ID 32) berhasil dibuat.');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Gagal membuat tiket: ' . $e->getMessage());
        }
    }
}