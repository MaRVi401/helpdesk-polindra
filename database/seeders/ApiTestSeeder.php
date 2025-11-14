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
        
        $prodi = ProgramStudi::where('program_studi', 'D3 - Teknik Informatika')->first();
        $unit = Unit::where('nama_unit', 'UPA TIK')->first();

        if (!$prodi || !$unit) {
            $this->command->error("Master data (Program Studi 'D3 - Teknik Informatika' atau Unit 'UPA TIK') tidak ditemukan. Harap pastikan MasterDataSeeder sudah dijalankan sebelum ApiTestSeeder.");
            return;
        }
        $userMhs = User::create([
            'name' => 'Budi Babiniksati',
            'email' => 'budibabi@student.polindra.ac.id',
            'password' => Hash::make('12345678'),
            'role' => 'mahasiswa',
        ]);

        Mahasiswa::create([
            'user_id' => $userMhs->id,
            'program_studi_id' => $prodi->id,
            'nim' => '201401001',
            'tahun_masuk' => 2020,
        ]);

        $layanan = Layanan::firstOrCreate(
            ['nama' => 'Permintaan Reset Akun E-Mail'],
            ['unit_id' => $unit->id, 'prioritas' => 1]
        );

        $tiket1 = Tiket::create([
            'no_tiket' => 'RMA-20251114-0001', 
            'pemohon_id' => $userMhs->id,
            'layanan_id' => $layanan->id,
            'deskripsi' => 'Lupa password email sejak minggu lalu.',
        ]);

        $tiket2 = Tiket::create([
            'no_tiket' => 'RMA-20251114-0002',
            'pemohon_id' => $userMhs->id,
            'layanan_id' => $layanan->id,
            'deskripsi' => 'Akun Office 365 terblokir.',
        ]);

        
        RiwayatStatusTiket::create([
            'tiket_id' => $tiket1->id,
            'user_id' => $userMhs->id, 
            'status' => 'Pending', 
        ]);
        
        RiwayatStatusTiket::create([
            'tiket_id' => $tiket1->id,
            'user_id' => $userMhs->id, 
            'status' => 'Pending', 
        ]);

        RiwayatStatusTiket::create([
            'tiket_id' => $tiket1->id,
            'user_id' => $userMhs->id,
            'status' => 'Diproses',
        ]);
        
        RiwayatStatusTiket::create([
            'tiket_id' => $tiket1->id,
            'user_id' => $userMhs->id,
            'status' => 'Selesai', 
        ]);
        
        
        RiwayatStatusTiket::create([
            'tiket_id' => $tiket2->id,
            'user_id' => $userMhs->id, 
            'status' => 'Pending', 
        ]);
        
        RiwayatStatusTiket::create([
            'tiket_id' => $tiket2->id,
            'user_id' => $userMhs->id,
            'status' => 'Pending',
        ]);

        RiwayatStatusTiket::create([
            'tiket_id' => $tiket2->id,
            'user_id' => $userMhs->id,
            'status' => 'Diproses', 
        ]);

    }
}