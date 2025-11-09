<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Mahasiswa;
use App\Models\ProgramStudi;

class MahasiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil ID dari program studi yang sudah ada di MasterDataSeeder
        $prodiRPL = ProgramStudi::where('program_studi', 'D4 - Rekayasa Perangkat Lunak')->first();
        $prodiTM = ProgramStudi::where('program_studi', 'D4 - Perancangan Manufaktur')->first();
        $prodiKP = ProgramStudi::where('program_studi', 'D3 - Keperawatan')->first();

        // Daftar nama mahasiswa untuk dibuat (disesuaikan dengan prodi yang ada)
        $data_mahasiswa = [
            // Mahasiswa Jurusan Teknik Informatika
            ['name' => 'AHMAD YASSIN HASAN AL BANA', 'nim' => '2305001', 'prodi' => $prodiRPL, 'tahun' => '2023'],
            ['name' => 'ALESIA QUIN FORTUNA SALSA NABILA', 'nim' => '2305002', 'prodi' => $prodiRPL, 'tahun' => '2023'],
            ['name' => 'ANANDA SALSABILA', 'nim' => '2305003', 'prodi' => $prodiRPL, 'tahun' => '2023'],
            ['name' => 'ANANG MARUF', 'nim' => '2305004', 'prodi' => $prodiRPL, 'tahun' => '2023'],
            ['name' => 'AULIA RISKI APRINA', 'nim' => '2305005', 'prodi' => $prodiRPL, 'tahun' => '2023'],
            ['name' => 'BAHIAGI', 'nim' => '2305006', 'prodi' => $prodiRPL, 'tahun' => '2023'],
            ['name' => 'BINTANG RIZOY ANDI AL KHALIFI', 'nim' => '2305007', 'prodi' => $prodiRPL, 'tahun' => '2023'],
            ['name' => 'DANIEL SINAGA', 'nim' => '2305008', 'prodi' => $prodiRPL, 'tahun' => '2023'],
            ['name' => 'EKA DAYA FADILAH JULIANSAH', 'nim' => '2305009', 'prodi' => $prodiRPL, 'tahun' => '2023'],
            ['name' => 'FADILLAH ROHMAN', 'nim' => '2305010', 'prodi' => $prodiRPL, 'tahun' => '2023'],
            ['name' => 'HAMZAH FAUZI PRATAMA', 'nim' => '2305011', 'prodi' => $prodiRPL, 'tahun' => '2023'],
            ['name' => 'INDRA', 'nim' => '2305012', 'prodi' => $prodiRPL, 'tahun' => '2023'],
            ['name' => 'KHOERUL PAROID', 'nim' => '2305013', 'prodi' => $prodiRPL, 'tahun' => '2023'],
            ['name' => 'LATIFAH', 'nim' => '2305014', 'prodi' => $prodiRPL, 'tahun' => '2023'],
            ['name' => 'MAHRUS RASHIF HIBBAN', 'nim' => '2305015', 'prodi' => $prodiRPL, 'tahun' => '2023'],
            ['name' => 'MOCHAMAD DIMAS TRIVIBOWO', 'nim' => '2305016', 'prodi' => $prodiRPL, 'tahun' => '2023'],
            ['name' => 'MUHAMAD HADID FALETEHAN', 'nim' => '2305017', 'prodi' => $prodiRPL, 'tahun' => '2023'],
            ['name' => 'MUHAMMAD IORO NEGORO', 'nim' => '2305018', 'prodi' => $prodiRPL, 'tahun' => '2023'],
            ['name' => 'NABA IMELDA NURUSSAUBA', 'nim' => '2305019', 'prodi' => $prodiRPL, 'tahun' => '2023'],
            ['name' => 'NAURA AZZAHRA BUDIYONO', 'nim' => '2305020', 'prodi' => $prodiRPL, 'tahun' => '2023'],
            ['name' => 'PAHRIL LESMANA', 'nim' => '2305021', 'prodi' => $prodiRPL, 'tahun' => '2023'],
            ['name' => 'PUTRI AYU FADHILAH', 'nim' => '2305022', 'prodi' => $prodiRPL, 'tahun' => '2023'],
            ['name' => 'RADEN GUNAWAN', 'nim' => '2305023', 'prodi' => $prodiRPL, 'tahun' => '2023'],
            ['name' => 'RAYHAN RAMADHANI', 'nim' => '2305024', 'prodi' => $prodiRPL, 'tahun' => '2023'],
            ['name' => 'SALSAH BILLAH', 'nim' => '2305025', 'prodi' => $prodiRPL, 'tahun' => '2023'],
            ['name' => 'THUFAIL NAZHIF NAFIS', 'nim' => '2305026', 'prodi' => $prodiRPL, 'tahun' => '2023'],
            ['name' => 'TSALTSA SIFA BILQIS SALAAMAH', 'nim' => '2305027', 'prodi' => $prodiRPL, 'tahun' => '2023'],
            ['name' => 'WUSTO HAMJAH', 'nim' => '2305028', 'prodi' => $prodiRPL, 'tahun' => '2023']
        ];

        foreach ($data_mahasiswa as $mahasiswa) {
            // Membuat email dari nama (lower case, ganti spasi dengan titik)
            $email = $mahasiswa['nim'] . '@student.polindra.ac.id';
            
            // Cek jika prodi ada, jika tidak lewati
            if (!$mahasiswa['prodi']) {
                $this->command->warn("Program studi untuk {$mahasiswa['name']} tidak ditemukan, data dilewati.");
                continue;
            }

            // Buat user baru dengan role 'mahasiswa'
            $user = User::create([
                'name' => $mahasiswa['name'],
                'email' => $email,
                'password' => bcrypt($mahasiswa['nim']), // Password default menggunakan NIM
                'role' => 'mahasiswa',
            ]);

            // Buat data 
            Mahasiswa::create([
                'user_id' => $user->id, // Ini yang benar
                'program_studi_id' => $mahasiswa['prodi']->id,
                'nim' => $mahasiswa['nim'],
                'tahun_masuk' => $mahasiswa['tahun'],
            ]);
        }
    }
}