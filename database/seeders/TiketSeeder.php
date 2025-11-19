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

            // ===============================
            // 1. Tiket Surat Keterangan Aktif
            // ===============================
            $tiket1 = Tiket::create([
                'no_tiket'   => 'TKT-' . Str::upper(Str::random(6)),
                'pemohon_id' => 1,      // id user pemohon
                'layanan_id' => 1,      // id layanan surat ket. aktif
                'deskripsi'  => 'Permohonan surat keterangan aktif kuliah untuk keperluan administrasi.',
            ]);

            DB::table('detail_tiket_surat_ket_aktif')->insert([
                'tiket_id'          => $tiket1->id,
                'keperluan'         => 'Beasiswa',
                'tahun_ajaran'      => 2024,
                'semester'          => 5,
                'keperluan_lainnya' => null,
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);


            // ===============================
            // 2. Tiket Reset Akun
            // ===============================
            $tiket2 = Tiket::create([
                'no_tiket'   => 'TKT-' . Str::upper(Str::random(6)),
                'pemohon_id' => 1,
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
            // 3. Tiket Ubah Data Mahasiswa
            // ===============================
            $tiket3 = Tiket::create([
                'no_tiket'   => 'TKT-' . Str::upper(Str::random(6)),
                'pemohon_id' => 1,
                'layanan_id' => 3,
                'deskripsi'  => 'Perubahan data di KTP dan data kampus.',
            ]);

            DB::table('detail_tiket_ubah_data_mhs')->insert([
                'tiket_id'          => $tiket3->id,
                'data_nama_lengkap' => 'Ahmad Yassin',
                'data_tmp_lahir'    => 'Indramayu',
                'data_tgl_lhr'      => '2003-11-22',
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);


            // ===============================
            // 4. Tiket Request Publikasi
            // ===============================
            $tiket4 = Tiket::create([
                'no_tiket'   => 'TKT-' . Str::upper(Str::random(6)),
                'pemohon_id' => 1,
                'layanan_id' => 4,
                'deskripsi'  => 'Permohonan publikasi kegiatan seminar.',
            ]);

            DB::table('detail_tiket_req_publikasi')->insert([
                'tiket_id' => $tiket4->id,
                'judul'    => 'Seminar Nasional Teknologi Informasi',
                'kategori' => 'Event',
                'konten'   => 'Seminar nasional dengan tema Teknologi Masa Depan.',
                'gambar'   => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

        });
    }
}
