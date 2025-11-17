<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Layanan;
use App\Models\Tiket;
use App\Models\DetailTiketReqPublikasi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TiketReqPublikasiSeeder extends Seeder
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
        
        // 2. Cari Layanan "Request Publikasi"
        $layanan = Layanan::where('nama', 'Request Publikasi')->first();

        if (!$mahasiswaUser || !$layanan) {
            $this->command->error('User ID 32 dengan role "mahasiswa" atau Layanan "Request Publikasi" tidak ditemukan. Seeder ini akan dilewati.');
            return;
        }

        DB::beginTransaction();
        try {
            // 3. Buat detail
            $detail = DetailTiketReqPublikasi::create([
                'judul' => 'HIMATIF Adakan Lomba Web Design 2025',
                'kategori' => 'Event',
                'konten' => 'Dalam rangka merayakan Dies Natalis, HIMATIF akan mengadakan lomba web design tingkat nasional. Pendaftaran dibuka mulai tanggal 20-30 November 2025.',
                // 'gambar' => 'lampiran_publikasi/poster_lomba.jpg'
            ]);

            // 4. Buat tiket
            $tiket = new Tiket([
                'pemohon_id' => $mahasiswaUser->id, // Langsung pakai ID 32
                'layanan_id' => $layanan->id,
                'deskripsi' => 'Mohon dipublikasikan berita event dari HIMATIF. Naskah berita dan detail sudah ada di form. Poster kami lampirkan di detail tiket.',
                'no_tiket' => 'TKT-' . time() . Str::random(4),
                'status' => 'menunggu',
                'prioritas' => 'rendah',
            ]);

            // 5. Asosiasikan
            $tiket->detail()->associate($detail);
            $tiket->save();

            DB::commit();
            $this->command->info('Tiket Request Publikasi (User ID 32) berhasil dibuat.');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Gagal membuat tiket: ' . $e->getMessage());
        }
    }
}