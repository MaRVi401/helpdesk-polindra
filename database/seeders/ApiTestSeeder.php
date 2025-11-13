<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Mahasiswa;
use App\Models\Layanan;
use App\Models\Tiket;
use App\Models\RiwayatStatusTiket;
use App\Models\ProgramStudi;
use App\Models\Unit;

class ApiTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Ambil ID data master yang relevan (diasumsikan sudah dibuat oleh MasterDataSeeder)
        
        // Cari Program Studi 'D3 - Teknik Informatika'
        $prodi = ProgramStudi::where('program_studi', 'D3 - Teknik Informatika')->first();
        // Cari Unit 'UPA TIK'
        $unit = Unit::where('nama_unit', 'UPA TIK')->first();

        // **Peringatan: Pastikan MasterDataSeeder sudah dijalankan**
        if (!$prodi || !$unit) {
            $this->command->error("Master data (Program Studi 'D3 - Teknik Informatika' atau Unit 'UPA TIK') tidak ditemukan. Harap pastikan MasterDataSeeder sudah dijalankan sebelum ApiTestSeeder.");
            return;
        }

        // 2. Buat Pengguna (Mahasiswa)
        $userMhs = User::create([
            'name' => 'Budi Sudarsono',
            'email' => 'budi.sudarsono@student.polindra.ac.id',
            'password' => Hash::make('password'),
            'role' => 'mahasiswa', // Pastikan kolom role ada di tabel users
        ]);

        Mahasiswa::create([
            'user_id' => $userMhs->id,
            'program_studi_id' => $prodi->id,
            'nim' => '201401001',
            'tahun_masuk' => 2020,
        ]);

        // 3. Buat Layanan (Jika belum ada, buat di sini. Jika sudah ada di LayananSeeder, 
        // Anda bisa memilih untuk mengambilnya. Di sini kita asumsikan membuat layanan baru 
        // agar relasi ke Tiket terjamin jika LayananSeeder belum dibuat/dipanggil.)
        
        // Cek apakah layanan sudah ada, jika tidak, buat
        $layanan = Layanan::firstOrCreate(
            ['nama' => 'Permintaan Reset Akun E-Mail'],
            ['unit_id' => $unit->id, 'prioritas' => 1]
        );

        // 4. Buat Tiket
        // Gunakan no_tiket yang unik untuk testing
        $tiket1 = Tiket::create([
            'no_tiket' => 123456, 
            'pemohon_id' => $userMhs->id,
            'layanan_id' => $layanan->id,
            'deskripsi' => 'Lupa password email sejak minggu lalu.',
        ]);

        // Tambahkan tiket lain untuk skenario testing
        $tiket2 = Tiket::create([
            'no_tiket' => 123457, 
            'pemohon_id' => $userMhs->id,
            'layanan_id' => $layanan->id,
            'deskripsi' => 'Akun Office 365 terblokir.',
        ]);


        // 5. Buat Riwayat Status Tiket untuk Tiket 1 (Selesai/Done)
        RiwayatStatusTiket::create([
            'tiket_id' => $tiket1->id,
            'user_id' => $userMhs->id, 
            'status' => 'Draft',
        ]);
        
        RiwayatStatusTiket::create([
            'tiket_id' => $tiket1->id,
            'user_id' => $userMhs->id, // Asumsi user yang mengajukan status awal (misal: "Diajukan")
            'status' => 'Pending',
        ]);

        RiwayatStatusTiket::create([
            'tiket_id' => $tiket1->id,
            'user_id' => $userMhs->id, // Ganti dengan ID User Admin/Staff di lingkungan nyata
            'status' => 'In Progress',
        ]);
        
        RiwayatStatusTiket::create([
            'tiket_id' => $tiket1->id,
            'user_id' => $userMhs->id, // Ganti dengan ID User Admin/Staff di lingkungan nyata
            'status' => 'Done',
        ]);
        
        // 6. Buat Riwayat Status Tiket untuk Tiket 2 (Masih Diproses/In Progress)
        RiwayatStatusTiket::create([
            'tiket_id' => $tiket2->id,
            'user_id' => $userMhs->id, 
            'status' => 'Draft',
        ]);
        
        RiwayatStatusTiket::create([
            'tiket_id' => $tiket2->id,
            'user_id' => $userMhs->id,
            'status' => 'Pending',
        ]);

        RiwayatStatusTiket::create([
            'tiket_id' => $tiket2->id,
            'user_id' => $userMhs->id,
            'status' => 'In Progress',
        ]);

    }
}