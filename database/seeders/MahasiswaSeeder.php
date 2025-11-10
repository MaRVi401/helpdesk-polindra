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
            ['name' => 'Ahmad Yassin Hasan Al Bana', 'nim' => '2305001', 'prodi' => $prodiRPL, 'tahun' => '2023'],
            ['name' => 'Alesia Quin Fortuna Salsa Nabila', 'nim' => '2305002', 'prodi' => $prodiRPL, 'tahun' => '2023'],
            ['name' => 'Ananda Salsabila', 'nim' => '2305003', 'prodi' => $prodiRPL, 'tahun' => '2023'],
            ['name' => 'Anang Maruf', 'nim' => '2305004', 'prodi' => $prodiRPL, 'tahun' => '2023'],
            ['name' => 'Aulia Riski Aprina', 'nim' => '2305005', 'prodi' => $prodiRPL, 'tahun' => '2023'],
            ['name' => 'Baihaqi', 'nim' => '2305006', 'prodi' => $prodiRPL, 'tahun' => '2023'],
            ['name' => 'Bintang Rizoy Andi Al Khalifi', 'nim' => '2305007', 'prodi' => $prodiRPL, 'tahun' => '2023'],
            ['name' => 'Daniel Sinaga', 'nim' => '2305008', 'prodi' => $prodiRPL, 'tahun' => '2023'],
            ['name' => 'Eka Daya Fadilah Juliansah', 'nim' => '2305009', 'prodi' => $prodiRPL, 'tahun' => '2023'],
            ['name' => 'Fadillah Rohman', 'nim' => '2305010', 'prodi' => $prodiRPL, 'tahun' => '2023'],
            ['name' => 'Hamzah Fauzi Pratama', 'nim' => '2305011', 'prodi' => $prodiRPL, 'tahun' => '2023'],
            ['name' => 'Indra', 'nim' => '2305012', 'prodi' => $prodiRPL, 'tahun' => '2023'],
            ['name' => 'Khoerul Paroid', 'nim' => '2305013', 'prodi' => $prodiRPL, 'tahun' => '2023'],
            ['name' => 'Latifah', 'nim' => '2305014', 'prodi' => $prodiRPL, 'tahun' => '2023'],
            ['name' => 'Mahrus Rashif Hibban', 'nim' => '2305015', 'prodi' => $prodiRPL, 'tahun' => '2023'],
            ['name' => 'Mochamad Dimas Trivibowo', 'nim' => '2305016', 'prodi' => $prodiRPL, 'tahun' => '2023'],
            ['name' => 'Muhamad Hadid Faletehan', 'nim' => '2305017', 'prodi' => $prodiRPL, 'tahun' => '2023'],
            ['name' => 'Muhammad Iqro Negoro', 'nim' => '2305018', 'prodi' => $prodiRPL, 'tahun' => '2023'],
            ['name' => 'Naba Imelda Nurussauba', 'nim' => '2305019', 'prodi' => $prodiRPL, 'tahun' => '2023'],
            ['name' => 'Naura Azzahra Budiyono', 'nim' => '2305020', 'prodi' => $prodiRPL, 'tahun' => '2023'],
            ['name' => 'Pahril Lesmana', 'nim' => '2305021', 'prodi' => $prodiRPL, 'tahun' => '2023'],
            ['name' => 'Putri Ayu Fadhilah', 'nim' => '2305022', 'prodi' => $prodiRPL, 'tahun' => '2023'],
            ['name' => 'Raden Gunawan', 'nim' => '2305023', 'prodi' => $prodiRPL, 'tahun' => '2023'],
            ['name' => 'Rayhan Ramadhani', 'nim' => '2305024', 'prodi' => $prodiRPL, 'tahun' => '2023'],
            ['name' => 'Salsah Billah', 'nim' => '2305025', 'prodi' => $prodiRPL, 'tahun' => '2023'],
            ['name' => 'Thufail Nazhif Nafis', 'nim' => '2305026', 'prodi' => $prodiRPL, 'tahun' => '2023'],
            ['name' => 'Tsaltsa Sifa Bilqis Salaamah', 'nim' => '2305027', 'prodi' => $prodiRPL, 'tahun' => '2023'],
            ['name' => 'Wusto Hamjah', 'nim' => '2305028', 'prodi' => $prodiRPL, 'tahun' => '2023'],
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