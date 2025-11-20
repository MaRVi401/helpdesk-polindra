<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Tiket;

class TiketSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            
            // Tentukan Tanggal Hari Ini (YYYYMMDD) dan Nomor Urut Awal
            $date = now()->format('Ymd'); // Contoh: 20251119
            $sequence = 1; // Mulai nomor urut dari 1

            // ===============================
            // 1. Tiket Surat Keterangan Aktif (SKA)
            // ===============================
            $tiket1 = Tiket::create([
                // Format: SKA-YYYYMMDD-0001
                'no_tiket'   => 'SKA-' . $date . '-' . str_pad($sequence++, 4, '0', STR_PAD_LEFT),
                'pemohon_id' => 15,      
                'layanan_id' => 1,       
                'deskripsi'  => 'Permohonan surat keterangan aktif kuliah untuk keperluan administrasi.',
            ]);

            DB::table('detail_tiket_surat_ket_aktif')->insert([
                'tiket_id'          => $tiket1->id,
                'keperluan'         => 'Beasiswa',
                'tahun_ajaran'      => 2024,
                'semester'          => 5,
                'keperluan_lainnya' => 'Diomelin yassin mulu',
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);


            // ===============================
            // 2. Tiket Reset Akun (RAM)
            // ===============================
            $tiket2 = Tiket::create([
                // Format: RAM-YYYYMMDD-0002
                'no_tiket'   => 'RAM-' . $date . '-' . str_pad($sequence++, 4, '0', STR_PAD_LEFT),
                'pemohon_id' => 15,
                'layanan_id' => 2,
                'deskripsi'  => 'Akun tidak bisa login sejak kemarin.',
            ]);

            DB::table('detail_tiket_reset_akun')->insert([
                'tiket_id'  => $tiket2->id,
                'aplikasi'  => 'gmail',
                'deskripsi' => 'Lupa password dan email pemulihan tidak aktif.',
                'created_at' => now(),
                'updated_at' => now(),
            ]);


            // ===============================
            // 3. Tiket Ubah Data Mahasiswa (UDM)
            // ===============================
            $tiket3 = Tiket::create([
                // Format: UDM-YYYYMMDD-0003
                'no_tiket'   => 'UDM-' . $date . '-' . str_pad($sequence++, 4, '0', STR_PAD_LEFT),
                'pemohon_id' => 15,
                'layanan_id' => 3,
                'deskripsi'  => 'Perubahan data di KTP dan data kampus.',
            ]);

            DB::table('detail_tiket_ubah_data_mhs')->insert([
                'tiket_id'          => $tiket3->id,
                'data_nama_lengkap' => 'Padilah Rohman Faletehan',
                'data_tmp_lahir'    => 'Indramayu',
                'data_tgl_lhr'      => '2003-11-22',
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);


            // ===============================
            // 4. Tiket Request Publikasi (RPK)
            // ===============================
            $tiket4 = Tiket::create([
                // Format: RPK-YYYYMMDD-0004
                'no_tiket'   => 'RPK-' . $date . '-' . str_pad($sequence++, 4, '0', STR_PAD_LEFT),
                'pemohon_id' => 15,
                'layanan_id' => 4,
                'deskripsi'  => 'Permohonan publikasi kegiatan seminar.',
            ]);

            DB::table('detail_tiket_req_publikasi')->insert([
                'tiket_id' => $tiket4->id,
                'judul'    => 'Seminar Nasional Teknologi Informasi',
                'kategori' => 'Event',
                'konten'   => 'Seminar nasional dengan tema Teknologi Masa Depan.',
                'gambar'   => 'lampiran-req-publikasi/testing.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

        });
    }
}